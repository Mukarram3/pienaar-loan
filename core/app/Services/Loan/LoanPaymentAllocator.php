<?php

// =============================================================
// File: app/Services/Loan/LoanPaymentAllocator.php
// =============================================================

declare(strict_types=1);

namespace App\Services\Loan;

use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanPaymentAllocation;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Enforces the contractual payment allocation clause:
 *
 *   "Each payment received shall be allocated as follows: 50% shall be applied
 *    towards repayment of the outstanding Capital Sum, and the remaining 50%
 *    shall be applied towards payment of interest due under the Loan."
 *
 * The 50/50 figures above are the standard plan. This class NEVER hard-codes
 * them — it applies whatever ratio the loan was contracted on (spec s.9).
 *
 * Order of operations for every payment:
 *
 *   1. Late charges are deducted FIRST and posted to their own ledger. They
 *      are never allocated to capital or profit, and never increase the
 *      Capital Sum (spec s.16).
 *   2. Any amount above the scheduled instalment is held as unallocated
 *      credit. It is NOT split, so an overpayment cannot manufacture profit
 *      the borrower never contracted to pay (spec s.13).
 *   3. What remains is split on the contracted ratio. A short payment is
 *      split on the same ratio rather than applied capital-first (spec s.12).
 *
 * All arithmetic is in integer cents.
 */
class LoanPaymentAllocator
{
    public const SOURCE_OPENING     = 'opening_balance';
    public const SOURCE_SCHEDULED   = 'scheduled_installment';
    public const SOURCE_MANUAL      = 'manual';
    public const SOURCE_SETTLEMENT  = 'settlement';

    private const DEFAULT_CAPITAL_RATIO = 0.5;
    private const DEFAULT_PROFIT_RATIO  = 0.5;

    // -----------------------------------------------------------------------
    // Contracted ratio
    // -----------------------------------------------------------------------

    /**
     * The allocation ratio this loan was contracted on.
     *
     * Resolution order: the loan's own snapshot, then the plan, then 50/50.
     * Reading the snapshot first is what stops a later plan edit from silently
     * re-splitting historic loans.
     *
     * @return array{capital: float, profit: float}
     */
    public function ratioFor(Loan $loan): array
    {
        if ($loan->capital_ratio !== null && $loan->profit_ratio !== null) {
            $capital = (float) $loan->capital_ratio;
            $profit  = (float) $loan->profit_ratio;
        } elseif ($loan->plan && $loan->plan->capital_ratio !== null) {
            $capital = (float) $loan->plan->capital_ratio;
            $profit  = (float) $loan->plan->profit_ratio;
        } else {
            $capital = self::DEFAULT_CAPITAL_RATIO;
            $profit  = self::DEFAULT_PROFIT_RATIO;
        }

        // Guard against a malformed configuration reaching the ledger.
        if ($capital <= 0 || abs(($capital + $profit) - 1.0) > 0.0001) {
            $capital = self::DEFAULT_CAPITAL_RATIO;
            $profit  = self::DEFAULT_PROFIT_RATIO;
        }

        return ['capital' => $capital, 'profit' => $profit];
    }

    /**
     * Freeze the plan's ratio onto the loan. Call once, at loan creation.
     */
    public function snapshotRatio(Loan $loan): void
    {
        if ($loan->capital_ratio !== null && $loan->profit_ratio !== null) {
            return; // already contracted
        }

        $ratio = $this->ratioFor($loan);

        $loan->capital_ratio = $ratio['capital'];
        $loan->profit_ratio  = $ratio['profit'];
    }

    // -----------------------------------------------------------------------
    // Scheduled totals
    // -----------------------------------------------------------------------

    /**
     * Total contractual repayment for this loan.
     *
     * For a legacy loan the administrator-entered total repayable is
     * authoritative — it is what the borrower's original agreement says. It is
     * used in preference to per_installment x total_installment.
     */
    public function totalRepayable(Loan $loan): float
    {
        if ($loan->is_legacy && $loan->total_repayable_override) {
            return (float) $loan->total_repayable_override;
        }

        return (float) $loan->per_installment * (int) $loan->total_installment;
    }

    /**
     * @return array{
     *   total_capital: float, total_profit: float,
     *   capital_repaid: float, profit_received: float,
     *   capital_outstanding: float, profit_outstanding: float,
     *   late_fees_paid: float, unallocated_credit: float,
     *   capital_ratio: float, profit_ratio: float, from_ledger: bool
     * }
     */
    public function summary(Loan $loan): array
    {
        $ratio    = $this->ratioFor($loan);
        $payable  = $this->totalRepayable($loan);

        $totalCapital = round($payable * $ratio['capital'], 2);
        $totalProfit  = round($payable - $totalCapital, 2);

        if ($loan->allocation_ledger_active) {
            $capitalRepaid  = (float) $loan->capital_repaid;
            $profitReceived = (float) $loan->profit_received;
            $fromLedger     = true;
        } else {
            // Pre-ledger loans: derive pro-rata as before so nothing on screen
            // changes for them.
            $paid           = (float) $loan->per_installment * (int) $loan->given_installment;
            $capitalRepaid  = round($paid * $ratio['capital'], 2);
            $profitReceived = round($paid - $capitalRepaid, 2);
            $fromLedger     = false;
        }

        return [
            'total_capital'       => $totalCapital,
            'total_profit'        => $totalProfit,
            'capital_repaid'      => $capitalRepaid,
            'profit_received'     => $profitReceived,
            'capital_outstanding' => round(max(0, $totalCapital - $capitalRepaid), 2),
            'profit_outstanding'  => round(max(0, $totalProfit - $profitReceived), 2),
            'late_fees_paid'      => (float) $loan->late_fees_paid,
            'unallocated_credit'  => (float) $loan->unallocated_credit,
            'capital_ratio'       => $ratio['capital'],
            'profit_ratio'        => $ratio['profit'],
            'from_ledger'         => $fromLedger,
        ];
    }

    // -----------------------------------------------------------------------
    // Recording payments
    // -----------------------------------------------------------------------

    /**
     * Record a received payment and post it to the ledger.
     *
     * @param float $amountReceived  Gross amount received, INCLUDING any late fee.
     * @param float $scheduledAmount Contractual instalment due, EXCLUDING late fees.
     * @param float $lateFeePortion  Late charge included in $amountReceived.
     */
    public function record(
        Loan $loan,
        float $amountReceived,
        float $scheduledAmount,
        float $lateFeePortion = 0.0,
        string $source = self::SOURCE_SCHEDULED,
        ?Installment $installment = null,
        ?string $notes = null
    ): LoanPaymentAllocation {
        if ($amountReceived < 0) {
            throw new RuntimeException('Payment amount cannot be negative.');
        }

        $ratio = $this->ratioFor($loan);

        $receivedCents  = $this->cents($amountReceived);
        $lateFeeCents   = min($this->cents($lateFeePortion), $receivedCents);
        $scheduledCents = $this->cents($scheduledAmount);

        // 1. Late charges off the top, to their own ledger.
        $afterFeesCents = $receivedCents - $lateFeeCents;

        // 2. Overpayment held, never split.
        $excessCents      = max(0, $afterFeesCents - $scheduledCents);
        $allocatableCents = $afterFeesCents - $excessCents;

        // 3. Split the remainder on the contracted ratio.
        $capitalCents = (int) round($allocatableCents * $ratio['capital']);
        $profitCents  = $allocatableCents - $capitalCents;

        return DB::transaction(function () use (
            $loan, $installment, $source, $notes, $ratio,
            $receivedCents, $scheduledCents, $lateFeeCents,
            $allocatableCents, $capitalCents, $profitCents, $excessCents
        ) {
            $loan->capital_ratio            ??= $ratio['capital'];
            $loan->profit_ratio             ??= $ratio['profit'];
            $loan->capital_repaid             = (float) $loan->capital_repaid + $this->major($capitalCents);
            $loan->profit_received            = (float) $loan->profit_received + $this->major($profitCents);
            $loan->late_fees_paid             = (float) $loan->late_fees_paid + $this->major($lateFeeCents);
            $loan->unallocated_credit         = (float) $loan->unallocated_credit + $this->major($excessCents);
            $loan->allocation_ledger_active   = true;
            $loan->save();

            $summary = $this->summary($loan->refresh());

            return LoanPaymentAllocation::create([
                'loan_id'                   => $loan->id,
                'installment_id'            => $installment?->id,
                'recorded_by'               => auth('admin')->id(),
                'source'                    => $source,
                'amount_received'           => $this->major($receivedCents),
                'scheduled_amount'          => $this->major($scheduledCents),
                'late_fee_portion'          => $this->major($lateFeeCents),
                'allocatable_amount'        => $this->major($allocatableCents),
                'capital_allocated'         => $this->major($capitalCents),
                'profit_allocated'          => $this->major($profitCents),
                'unallocated_excess'        => $this->major($excessCents),
                'capital_ratio_applied'     => $ratio['capital'],
                'profit_ratio_applied'      => $ratio['profit'],
                'capital_outstanding_after' => $summary['capital_outstanding'],
                'profit_outstanding_after'  => $summary['profit_outstanding'],
                'is_partial'                => $allocatableCents < $scheduledCents,
                'is_overpayment'            => $excessCents > 0,
                'value_date'                => now()->toDateString(),
                'notes'                     => $notes,
            ]);
        });
    }

    /**
     * Seed the ledger for an imported legacy loan.
     *
     * Records, as a single opening entry, the split of what the borrower had
     * ALREADY paid before import. This does not change any balance — it states
     * the capital/profit composition of a figure the administrator entered.
     *
     * Idempotent: refuses to run twice on the same loan.
     */
    public function seedOpeningBalance(Loan $loan, ?float $alreadyPaid = null): ?LoanPaymentAllocation
    {
        if ($loan->allocation_ledger_active) {
            return null;
        }

        $this->snapshotRatio($loan);

        $paid = $alreadyPaid
            ?? ((float) $loan->per_installment * (int) $loan->given_installment);

        if ($paid <= 0) {
            // Nothing repaid yet. Activate the ledger with zero balances so
            // future payments are captured.
            $loan->capital_repaid           = 0;
            $loan->profit_received          = 0;
            $loan->allocation_ledger_active = true;
            $loan->save();

            return null;
        }

        return $this->record(
            loan:            $loan,
            amountReceived:  $paid,
            scheduledAmount: $paid,
            lateFeePortion:  0.0,
            source:          self::SOURCE_OPENING,
            installment:     null,
            notes: sprintf(
                'Opening balance at legacy import: %d of %d instalments already paid. '
                . 'Allocated %s%% capital / %s%% profit per the contracted allocation. '
                . 'This entry records the composition of an existing balance and does '
                . 'not alter it.',
                (int) $loan->given_installment,
                (int) $loan->total_installment,
                rtrim(rtrim(number_format((float) $loan->capital_ratio * 100, 2), '0'), '.'),
                rtrim(rtrim(number_format((float) $loan->profit_ratio * 100, 2), '0'), '.')
            )
        );
    }

    /**
     * The clause as it should read on the agreement, with the loan's actual
     * contracted percentages substituted.
     */
    public function clauseText(Loan $loan): string
    {
        $ratio = $this->ratioFor($loan);

        return sprintf(
            'Each payment received shall be allocated as follows: %s%% shall be applied '
            . 'towards repayment of the outstanding Capital Sum, and the remaining %s%% '
            . 'shall be applied towards payment of interest due under the Loan. Late '
            . 'payment charges, where applicable, are payable in addition and are not '
            . 'applied towards either the Capital Sum or interest.',
            $this->pct($ratio['capital']),
            $this->pct($ratio['profit'])
        );
    }

    public function pct(float $ratio): string
    {
        return rtrim(rtrim(number_format($ratio * 100, 2), '0'), '.');
    }

    private function cents(float $major): int
    {
        return (int) round($major * 100);
    }

    private function major(int $cents): float
    {
        return $cents / 100;
    }
}
