<?php

// =============================================================
// File: app/Services/Loan/LoanQuote.php
// =============================================================
declare(strict_types=1);

namespace App\Services\Loan;

/**
 * The single authoritative set of figures for a loan.
 *
 * Every consumer -- quotation screen, loan creation, repayment schedule,
 * borrower dashboard, admin dashboard, PDF agreement, statements -- reads from
 * this object. No consumer recomputes (spec s.20).
 *
 * All monetary values are exposed as floats in major units (rand), already
 * rounded. Internally the calculator works in integer cents, so these values
 * reconcile exactly.
 */
final class LoanQuote
{
    /**
     * @param ScheduledInstallment[] $schedule
     * @param string[]               $warnings
     */
    public function __construct(
        public readonly LoanTerms $terms,

        // Principal
        public readonly float $principal,

        // Per-instalment breakdown (the regular instalment; the final
        // instalment may differ by cents -- see $schedule).
        public readonly float $capitalPerInstallment,
        public readonly float $profitPerInstallment,
        public readonly float $totalPerInstallment,

        // Full-term totals
        public readonly float $totalCapitalRepayable,
        public readonly float $totalProfitOverTerm,
        public readonly float $totalContractualRepayment,

        // Charges -- deliberately NOT part of the contractual repayment
        // (spec s.15). Reported alongside, never added in.
        public readonly float $applicationFee,

        public readonly int   $totalInstallments,
        public readonly int   $installmentIntervalDays,

        public readonly array $schedule,
        public readonly array $warnings,
    ) {
    }

    /**
     * Effective cost of credit as a % of principal, for disclosure.
     */
    public function profitAsPctOfPrincipal(): float
    {
        return $this->principal > 0
            ? ($this->totalProfitOverTerm / $this->principal) * 100
            : 0.0;
    }

    public function hasWarnings(): bool
    {
        return $this->warnings !== [];
    }

    /**
     * Internal consistency assertion. Every figure must reconcile exactly.
     * Cheap enough to call in tests and in a deployment smoke check.
     *
     * @return string[] list of failures; empty means the quote reconciles
     */
    public function reconcile(): array
    {
        $errors = [];
        $cent   = 0.005; // half a cent

        $scheduleCapital = 0.0;
        $scheduleProfit  = 0.0;
        $scheduleTotal   = 0.0;

        foreach ($this->schedule as $row) {
            $scheduleCapital += $row->capital;
            $scheduleProfit  += $row->profit;
            $scheduleTotal   += $row->total;

            if (abs(($row->capital + $row->profit) - $row->total) > $cent) {
                $errors[] = sprintf(
                    'Instalment %d: capital %.2f + profit %.2f != total %.2f',
                    $row->number, $row->capital, $row->profit, $row->total
                );
            }
        }

        if (abs($scheduleCapital - $this->totalCapitalRepayable) > $cent) {
            $errors[] = sprintf(
                'Schedule capital %.2f != declared total capital %.2f',
                $scheduleCapital, $this->totalCapitalRepayable
            );
        }

        if (abs($scheduleProfit - $this->totalProfitOverTerm) > $cent) {
            $errors[] = sprintf(
                'Schedule profit %.2f != declared total profit %.2f',
                $scheduleProfit, $this->totalProfitOverTerm
            );
        }

        if (abs($scheduleTotal - $this->totalContractualRepayment) > $cent) {
            $errors[] = sprintf(
                'Schedule total %.2f != declared contractual repayment %.2f',
                $scheduleTotal, $this->totalContractualRepayment
            );
        }

        if (abs(($this->totalCapitalRepayable + $this->totalProfitOverTerm)
                - $this->totalContractualRepayment) > $cent) {
            $errors[] = 'Total capital + total profit != total contractual repayment';
        }

        if (count($this->schedule) !== $this->totalInstallments) {
            $errors[] = sprintf(
                'Schedule has %d rows, expected %d',
                count($this->schedule), $this->totalInstallments
            );
        }

        return $errors;
    }

    /**
     * Flat array for PDF templates and API responses. The PDF generator must
     * consume these keys rather than recomputing (spec s.19).
     */
    public function toArray(): array
    {
        return [
            'principal'                    => $this->principal,
            'capital_per_installment'      => $this->capitalPerInstallment,
            'profit_per_installment'       => $this->profitPerInstallment,
            'total_per_installment'        => $this->totalPerInstallment,
            'total_capital_repayable'      => $this->totalCapitalRepayable,
            'total_profit_over_term'       => $this->totalProfitOverTerm,
            'total_contractual_repayment'  => $this->totalContractualRepayment,
            'application_fee'              => $this->applicationFee,
            'total_installments'           => $this->totalInstallments,
            'installment_interval_days'    => $this->installmentIntervalDays,
            'capital_allocation_pct'       => $this->terms->capitalRatio * 100,
            'profit_allocation_pct'        => $this->terms->profitRatio * 100,
            'per_installment_capital_pct'  => $this->terms->perInstallmentPct,
        ];
    }
}
