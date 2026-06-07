<?php

namespace App\Services;

use App\Constants\Status;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanLifecycleEvent;
use App\Models\LoanSettlement;
use App\Models\RedemptionQuote;
use App\Models\SettlementPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LoanLifecycleService
{
    /**
     * Stage 2 → Mark quote as offered + record on loan.
     * Called from redemptionQuote() controller after quote creation.
     */
    public function attachQuote(Loan $loan, RedemptionQuote $quote): void
    {
        DB::transaction(function () use ($loan, $quote) {
            $fromStage = $loan->lifecycle_stage;
            $loan->active_quote_id = $quote->id;
            $loan->lifecycle_stage = Status::LIFECYCLE_REDEMPTION_OFFERED;
            $loan->save();

            LoanLifecycleEvent::log(
                $loan->id,
                'quote_issued',
                $fromStage,
                Status::LIFECYCLE_REDEMPTION_OFFERED,
                'Redemption quote issued: ' . $quote->quote_reference,
                [
                    'quote_reference'   => $quote->quote_reference,
                    'settlement_amount' => $quote->settlement_amount,
                    'expires_at'        => $quote->expires_at?->toDateTimeString(),
                ]
            );
        });
    }

    /**
     * Stage 3 — Accept redemption offer.
     */
    public function acceptQuote(Loan $loan): array
    {
        if (!$loan->canAcceptQuote()) {
            return ['ok' => false, 'message' => 'Loan is not in a state where quote can be accepted, or quote has expired.'];
        }

        DB::transaction(function () use ($loan) {
            $fromStage = $loan->lifecycle_stage;
            $loan->lifecycle_stage    = Status::LIFECYCLE_REDEMPTION_ACCEPTED;
            $loan->quote_accepted_at = now();
            $loan->quote_accepted_by = auth('admin')->id();
            $loan->save();

            $loan->activeQuote->update(['status' => Status::QUOTE_ACCEPTED]);

            LoanLifecycleEvent::log(
                $loan->id,
                'quote_accepted',
                $fromStage,
                Status::LIFECYCLE_REDEMPTION_ACCEPTED,
                'Redemption offer accepted. Settlement amount locked.',
                ['quote_id' => $loan->active_quote_id]
            );
        });

        return ['ok' => true, 'message' => 'Redemption offer accepted. Awaiting settlement payment.'];
    }

    /**
     * Stage 4 — Record settlement payment.
     */
    public function recordSettlementPayment(Loan $loan, array $data): array
    {
        if (!$loan->canRecordSettlementPayment()) {
            return ['ok' => false, 'message' => 'Loan is not awaiting settlement payment.'];
        }

        $quote = $loan->activeQuote;
        if (!$quote) {
            return ['ok' => false, 'message' => 'No active quote attached.'];
        }

        $expected = (float) $quote->settlement_amount;
        $received = (float) $data['received_amount'];
        $shortfall = max(0, $expected - $received);

        // Tolerance: 0.01 currency unit
        $isFull = $shortfall < 0.01;
        $acceptShort = !empty($data['accept_short']);

        if (!$isFull && !$acceptShort) {
            return [
                'ok'        => false,
                'message'   => "Received {$received} is short of expected {$expected} by {$shortfall}. Tick 'accept short payment' to override.",
                'shortfall' => $shortfall,
            ];
        }

        DB::transaction(function () use ($loan, $quote, $expected, $received, $shortfall, $data, $isFull) {
            $payment = SettlementPayment::create([
                'loan_id'           => $loan->id,
                'quote_id'          => $quote->id,
                'recorded_by'       => auth('admin')->id(),
                'expected_amount'   => $expected,
                'received_amount'   => $received,
                'shortfall'         => $shortfall,
                'payment_date'      => $data['payment_date'],
                'payment_method'    => $data['payment_method'] ?? null,
                'payment_reference' => $data['payment_reference'] ?? null,
                'status'            => $isFull ? Status::PAYMENT_FULL : Status::PAYMENT_SHORT,
                'notes'             => $data['notes'] ?? null,
            ]);

            // Loan settled
            $fromStage = $loan->lifecycle_stage;
            $loan->lifecycle_stage = Status::LIFECYCLE_SETTLED;
            $loan->status          = Status::LOAN_PAID;
            $loan->settled_at      = now();
            $loan->settled_by      = auth('admin')->id();
            $loan->given_installment = $loan->total_installment; // mark all paid
            $loan->save();

            // Cancel future installments and penalties
            Installment::where('loan_id', $loan->id)
                ->whereNull('given_at')
                ->update([
                    'given_at' => now(),
                    'delay_charge' => 0,
                ]);

            $quote->update(['status' => Status::QUOTE_SETTLED]);

            LoanLifecycleEvent::log(
                $loan->id,
                'settlement_payment_received',
                $fromStage,
                Status::LIFECYCLE_SETTLED,
                'Settlement payment received. Loan settled in full.',
                [
                    'payment_id'    => $payment->id,
                    'expected'      => $expected,
                    'received'      => $received,
                    'shortfall'     => $shortfall,
                    'accepted_short'=> !$isFull,
                ]
            );
        });

        return ['ok' => true, 'message' => 'Settlement payment recorded. Loan marked as Settled.'];
    }

    /**
     * Stage 5 — Close account.
     */
    public function closeAccount(Loan $loan): array
    {
        if (!$loan->canCloseAccount()) {
            return ['ok' => false, 'message' => 'Loan must be Settled before it can be Closed.'];
        }

        DB::transaction(function () use ($loan) {
            $fromStage = $loan->lifecycle_stage;
            $loan->lifecycle_stage = Status::LIFECYCLE_CLOSED;
            $loan->closed_at       = now();
            $loan->closed_by       = auth('admin')->id();
            $loan->save();

            // Auto-create settlement record so the certificate can pull stable refs
            LoanSettlement::firstOrCreate(
                ['loan_id' => $loan->id],
                [
                    'settlement_reference'   => 'SET-' . strtoupper(Str::random(8)) . '-' . $loan->id,
                    'certificate_reference'  => 'CERT-' . strtoupper(Str::random(8)) . '-' . $loan->id,
                    'issued_by'              => auth('admin')->id(),
                    'original_loan_amount'   => (float) $loan->amount,
                    'total_repaid'           => (float) $loan->payable_amount,
                    'final_settlement_date'  => $loan->settled_at ?? now(),
                    'closure_effective_date' => now(),
                    'settlement_type'        => $loan->settlementPayments()->exists() ? 2 : 1,
                ]
            );

            LoanLifecycleEvent::log(
                $loan->id,
                'account_closed',
                $fromStage,
                Status::LIFECYCLE_CLOSED,
                'Loan account closed. No further activity permitted.'
            );
        });

        return ['ok' => true, 'message' => 'Account closed. Settlement certificate available.'];
    }

    /**
     * Stage 6 — Release security.
     */
    public function releaseSecurity(Loan $loan, array $data): array
    {
        if (!$loan->canReleaseSecurity()) {
            return ['ok' => false, 'message' => 'Account must be Closed before security can be released.'];
        }

        DB::transaction(function () use ($loan, $data) {
            $fromStage = $loan->lifecycle_stage;
            $loan->lifecycle_stage         = Status::LIFECYCLE_SECURITY_RELEASED;
            $loan->security_released_at    = now();
            $loan->security_released_by    = auth('admin')->id();
            $loan->security_release_notes  = $data['notes'] ?? null;
            $loan->save();

            LoanLifecycleEvent::log(
                $loan->id,
                'security_released',
                $fromStage,
                Status::LIFECYCLE_SECURITY_RELEASED,
                'Security released to borrower.',
                [
                    'security_returned'   => !empty($data['security_returned']),
                    'lien_released'       => !empty($data['lien_released']),
                    'documents_returned'  => !empty($data['documents_returned']),
                ]
            );
        });

        return ['ok' => true, 'message' => 'Security release recorded.'];
    }

    /**
     * Cancel/void an active quote.
     */
    public function voidQuote(Loan $loan, ?string $reason = null): array
    {
        if ($loan->lifecycle_stage != Status::LIFECYCLE_REDEMPTION_OFFERED) {
            return ['ok' => false, 'message' => 'No active redemption offer to void.'];
        }

        DB::transaction(function () use ($loan, $reason) {
            $loan->activeQuote?->update(['status' => Status::QUOTE_VOID]);
            $loan->active_quote_id = null;
            $loan->lifecycle_stage = Status::LIFECYCLE_ACTIVE;
            $loan->save();

            LoanLifecycleEvent::log(
                $loan->id,
                'quote_voided',
                Status::LIFECYCLE_REDEMPTION_OFFERED,
                Status::LIFECYCLE_ACTIVE,
                $reason ?? 'Quote voided by admin.'
            );
        });

        return ['ok' => true, 'message' => 'Redemption offer voided. Loan returned to Active.'];
    }
}
