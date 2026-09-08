<?php

// =============================================================
// File: app/Services/Loan/LoanCalculator.php
// =============================================================
declare(strict_types=1);

namespace App\Services\Loan;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * THE authoritative calculation engine for Fixed Capital Repayment /
 * Legacy Fixed Term loans.
 *
 * Commercial model (spec s.3-s.6):
 *
 *   capitalComponent = principal x perInstallmentPct
 *   totalInstalment  = capitalComponent / capitalRatio
 *   profitComponent  = totalInstalment - capitalComponent
 *
 * Note that profitComponent is derived by SUBTRACTION rather than by
 * multiplying by profitRatio. The two are algebraically identical whenever
 * capitalRatio + profitRatio = 1 (which LoanTerms enforces), but subtraction
 * guarantees capital + profit == total to the cent with no rounding drift.
 *
 * All arithmetic is performed in integer cents. Floats are used only at the
 * boundary. This is why the reconciliation in LoanQuote::reconcile() is exact
 * rather than tolerance-based.
 *
 * No monetary amount is hard-coded anywhere in this class (spec s.8).
 */
final class LoanCalculator
{
    /**
     * Price a loan.
     *
     * @param float                  $principal  Amount advanced, in major units.
     * @param LoanTerms              $terms      Validated plan terms.
     * @param DateTimeImmutable|null $firstDue   First instalment due date. When
     *                                           null the schedule carries day
     *                                           offsets only.
     */
    public function quote(
        float $principal,
        LoanTerms $terms,
        ?DateTimeImmutable $firstDue = null
    ): LoanQuote {
        if ($principal <= 0) {
            throw new InvalidArgumentException('Loan amount must be greater than zero.');
        }

        $principalCents = $this->toCents($principal);
        $n              = $terms->totalInstallments;

        // --- Step 1: capital component of one instalment (spec s.3) ---------
        $capitalPerCents = (int) round($principalCents * $terms->perInstallmentPct / 100);

        if ($capitalPerCents <= 0) {
            throw new InvalidArgumentException(
                'The capital component rounds to zero. The loan amount is too small for '
                . 'this plan\'s Per Instalment %.'
            );
        }

        // --- Step 2: complete instalment (spec s.4) -------------------------
        $totalPerCents = (int) round($capitalPerCents / $terms->capitalRatio);

        // --- Step 3: profit component (spec s.5) ----------------------------
        $profitPerCents = $totalPerCents - $capitalPerCents;

        // --- Full-term totals, computed independently of the per-instalment
        //     rounding so that the schedule can be reconciled back to them.
        //     For a balanced plan (pct x n = 100) this is exactly the principal.
        $totalCapitalCents = (int) round(
            $principalCents * $terms->perInstallmentPct * $n / 100
        );
        $totalRepaymentCents = (int) round($totalCapitalCents / $terms->capitalRatio);
        $totalProfitCents    = $totalRepaymentCents - $totalCapitalCents;

        // --- Schedule. The final instalment absorbs the rounding residual. ---
        $schedule = $this->buildSchedule(
            $n,
            $capitalPerCents,
            $profitPerCents,
            $totalCapitalCents,
            $totalProfitCents,
            $terms->installmentIntervalDays,
            $firstDue
        );

        // --- Application fee: reported, never added to the repayment
        //     (spec s.15). ------------------------------------------------
        $applicationFeeCents = $this->toCents($terms->applicationFixedCharge)
            + (int) round($principalCents * $terms->applicationPercentCharge / 100);

        return new LoanQuote(
            terms:                     $terms,
            principal:                 $this->toMajor($principalCents),
            capitalPerInstallment:     $this->toMajor($capitalPerCents),
            profitPerInstallment:      $this->toMajor($profitPerCents),
            totalPerInstallment:       $this->toMajor($totalPerCents),
            totalCapitalRepayable:     $this->toMajor($totalCapitalCents),
            totalProfitOverTerm:       $this->toMajor($totalProfitCents),
            totalContractualRepayment: $this->toMajor($totalRepaymentCents),
            applicationFee:            $this->toMajor($applicationFeeCents),
            totalInstallments:         $n,
            installmentIntervalDays:   $terms->installmentIntervalDays,
            schedule:                  $schedule,
            warnings:                  $terms->warnings(),
        );
    }

    /**
     * @return ScheduledInstallment[]
     */
    private function buildSchedule(
        int $n,
        int $capitalPerCents,
        int $profitPerCents,
        int $totalCapitalCents,
        int $totalProfitCents,
        int $intervalDays,
        ?DateTimeImmutable $firstDue
    ): array {
        $rows              = [];
        $capitalRemaining  = $totalCapitalCents;
        $capitalRunningOut = $totalCapitalCents;

        for ($i = 1; $i <= $n; $i++) {
            $isFinal = ($i === $n);

            if ($isFinal) {
                // Absorb residual so the schedule sums exactly to the totals.
                $capital = $capitalRemaining;
                $profit  = $totalProfitCents - ($profitPerCents * ($n - 1));
            } else {
                $capital = $capitalPerCents;
                $profit  = $profitPerCents;
            }

            $capitalRemaining  -= $capital;
            $capitalRunningOut -= $capital;

            $dayOffset = $intervalDays * ($i - 1);
            $dueDate   = $firstDue?->modify("+{$dayOffset} days")->format('Y-m-d');

            $rows[] = new ScheduledInstallment(
                number:                  $i,
                capital:                 $this->toMajor($capital),
                profit:                  $this->toMajor($profit),
                total:                   $this->toMajor($capital + $profit),
                dayOffset:               $dayOffset,
                dueDate:                 $dueDate,
                capitalOutstandingAfter: $this->toMajor(max(0, $capitalRunningOut)),
                isFinal:                 $isFinal,
            );
        }

        return $rows;
    }

    /**
     * Allocate a received payment across the capital and profit ledgers
     * (spec s.11, s.12).
     *
     * Allocation is strictly pro-rata to the plan's configured ratio. A short
     * payment is split on the same ratio -- it is NOT applied capital-first.
     * Per spec s.12 that behaviour is only correct in the absence of a
     * separate documented payment-allocation rule.
     *
     * Overpayments are NOT handled here. This method flags them and refuses to
     * allocate the excess, because no existing PienaarBank overpayment policy
     * was found in the codebase and inventing one is out of scope (spec s.13).
     *
     * @param float $amountReceived   What the borrower actually paid.
     * @param float $scheduledAmount  What was due for this instalment.
     */
    public function allocatePayment(
        float $amountReceived,
        float $scheduledAmount,
        LoanTerms $terms
    ): array {
        if ($amountReceived < 0) {
            throw new InvalidArgumentException('Payment amount cannot be negative.');
        }

        $receivedCents  = $this->toCents($amountReceived);
        $scheduledCents = $this->toCents($scheduledAmount);

        $isOverpayment = $receivedCents > $scheduledCents;
        $excessCents   = max(0, $receivedCents - $scheduledCents);

        // Only the scheduled portion is allocated. Any excess is held pending
        // policy -- see spec s.13. Allocating it here would create profit the
        // borrower never contracted to pay.
        $allocatableCents = min($receivedCents, $scheduledCents);

        $capitalCents = (int) round($allocatableCents * $terms->capitalRatio);
        $profitCents  = $allocatableCents - $capitalCents;

        return [
            'received'            => $this->toMajor($receivedCents),
            'scheduled'           => $this->toMajor($scheduledCents),
            'allocated'           => $this->toMajor($allocatableCents),
            'capital_allocated'   => $this->toMajor($capitalCents),
            'profit_allocated'    => $this->toMajor($profitCents),
            'shortfall'           => $this->toMajor(max(0, $scheduledCents - $receivedCents)),
            'is_partial'          => $receivedCents < $scheduledCents,
            'is_overpayment'      => $isOverpayment,
            'unallocated_excess'  => $this->toMajor($excessCents),
            'requires_policy'     => $isOverpayment,
        ];
    }

    /**
     * Break an early settlement into its separable components (spec s.14).
     *
     * This deliberately does NOT return a settlement figure. It returns the
     * parts, so that whatever commercial methodology is confirmed can be
     * applied on top without the engine having to guess. The existing
     * hard-coded 50% / 75% haircuts are not reproduced here.
     *
     * @param float $capitalRepaid   Capital actually received to date.
     * @param float $profitReceived  Profit actually received to date.
     * @param float $arrears         Outstanding late charges (separate ledger, spec s.16).
     * @param float $fees            Outstanding fees (separate ledger, spec s.15).
     */
    public function settlementComponents(
        LoanQuote $quote,
        float $capitalRepaid,
        float $profitReceived,
        float $arrears = 0.0,
        float $fees = 0.0
    ): array {
        $totalCapitalCents  = $this->toCents($quote->totalCapitalRepayable);
        $totalProfitCents   = $this->toCents($quote->totalProfitOverTerm);
        $capitalRepaidCents = $this->toCents($capitalRepaid);
        $profitPaidCents    = $this->toCents($profitReceived);

        $outstandingCapitalCents = max(0, $totalCapitalCents - $capitalRepaidCents);
        $futureProfitCents       = max(0, $totalProfitCents - $profitPaidCents);

        return [
            'original_principal'     => $quote->principal,
            'capital_repaid'         => $this->toMajor($capitalRepaidCents),
            'outstanding_capital'    => $this->toMajor($outstandingCapitalCents),
            'profit_earned_to_date'  => $this->toMajor($profitPaidCents),
            'future_scheduled_profit'=> $this->toMajor($futureProfitCents),
            'fees_outstanding'       => $fees,
            'arrears_outstanding'    => $arrears,

            // Deliberately absent: 'early_settlement_amount'.
            // Awaiting confirmation of commercial methodology (spec s.14).
            'settlement_amount'      => null,
            'methodology_confirmed'  => false,
        ];
    }

    private function toCents(float $major): int
    {
        return (int) round($major * 100);
    }

    private function toMajor(int $cents): float
    {
        return $cents / 100;
    }
}
