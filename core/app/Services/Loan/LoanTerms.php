<?php

// =============================================================
// File: app/Services/Loan/LoanTerms.php
// =============================================================
declare(strict_types=1);

namespace App\Services\Loan;

use InvalidArgumentException;

/**
 * Immutable, validated representation of a Fixed Capital Repayment plan's
 * commercial terms.
 *
 * Deliberately framework-free: no Eloquent, no facades, no container. This
 * lets the same object be constructed from a LoanPlan row, from an admin form
 * before save, or from a bare array in a test.
 *
 * Terminology (see spec s.3-s.5):
 *   perInstallmentPct  % of ORIGINAL CAPITAL extinguished each interval.
 *                      It is NOT the instalment, and NOT the finance charge.
 *   capitalRatio       Share of the COMPLETE instalment that is capital.
 *   profitRatio        Share of the COMPLETE instalment that is profit.
 */
final class LoanTerms
{
    private const RATIO_TOLERANCE = 0.000001;

    public function __construct(
        public readonly float $perInstallmentPct,
        public readonly float $capitalRatio,
        public readonly float $profitRatio,
        public readonly int   $totalInstallments,
        public readonly int   $installmentIntervalDays,
        public readonly float $applicationFixedCharge   = 0.0,
        public readonly float $applicationPercentCharge = 0.0,
    ) {
        $this->guard();
    }

    /**
     * Build from a LoanPlan-shaped array. Accepts ratios either as decimals
     * (0.5, the DB storage format) or as percentages (50, the admin form
     * format), because both representations exist in the current codebase.
     */
    public static function fromPlan(array $plan): self
    {
        return new self(
            perInstallmentPct:        (float) ($plan['per_installment'] ?? 0),
            capitalRatio:             self::normaliseRatio($plan['capital_ratio'] ?? null, 'capital_ratio'),
            profitRatio:              self::normaliseRatio($plan['profit_ratio'] ?? null, 'profit_ratio'),
            totalInstallments:        (int) ($plan['total_installment'] ?? 0),
            installmentIntervalDays:  (int) ($plan['installment_interval'] ?? 0),
            applicationFixedCharge:   (float) ($plan['application_fixed_charge'] ?? 0),
            applicationPercentCharge: (float) ($plan['application_percent_charge'] ?? 0),
        );
    }

    private static function normaliseRatio(mixed $value, string $field): float
    {
        if ($value === null || $value === '') {
            throw new InvalidArgumentException("{$field} is not configured on this plan.");
        }

        $value = (float) $value;

        // Values above 1 can only sensibly be a percentage (e.g. 50 -> 0.50).
        return $value > 1.0 ? $value / 100.0 : $value;
    }

    /**
     * Hard validation. These throw: a plan that fails them must not be saved
     * and must not be used to price a loan (spec s.9).
     */
    private function guard(): void
    {
        if ($this->perInstallmentPct <= 0) {
            throw new InvalidArgumentException('Per Instalment % must be greater than zero.');
        }

        if ($this->capitalRatio <= 0) {
            throw new InvalidArgumentException(
                'Capital Allocation must be greater than 0%. A zero capital allocation would '
                . 'mean the instalment never repays principal.'
            );
        }

        if ($this->profitRatio < 0) {
            throw new InvalidArgumentException('Profit Allocation cannot be negative.');
        }

        $sum = $this->capitalRatio + $this->profitRatio;
        if (abs($sum - 1.0) > self::RATIO_TOLERANCE) {
            throw new InvalidArgumentException(sprintf(
                'Capital Allocation (%.2f%%) + Profit Allocation (%.2f%%) must equal 100%%. '
                . 'Currently %.2f%%. Correct the allocation before saving; values are not '
                . 'adjusted automatically.',
                $this->capitalRatio * 100,
                $this->profitRatio * 100,
                $sum * 100
            ));
        }

        if ($this->totalInstallments < 1) {
            throw new InvalidArgumentException('Total Instalments must be at least 1.');
        }

        if ($this->installmentIntervalDays < 1) {
            throw new InvalidArgumentException('Instalment Interval must be at least 1 day.');
        }
    }

    /**
     * Total % of original capital scheduled for repayment across the full term.
     * Should be 100.0 for a correctly configured plan (spec s.10).
     */
    public function scheduledCapitalRecoveryPct(): float
    {
        return $this->perInstallmentPct * $this->totalInstallments;
    }

    /**
     * Soft validation. Returns advisory messages for the administrator.
     * These do NOT throw and do NOT alter the entered values (spec s.10).
     *
     * @return string[]
     */
    public function warnings(): array
    {
        $warnings = [];
        $recovery = $this->scheduledCapitalRecoveryPct();

        if (abs($recovery - 100.0) > 0.01) {
            $implied  = $this->perInstallmentPct > 0
                ? 100.0 / $this->perInstallmentPct
                : 0.0;

            $warnings[] = sprintf(
                'Capital recovery check: %.4f%% per instalment x %d instalments = %.2f%% of '
                . 'original capital (expected 100%%). %s At this rate capital is extinguished '
                . 'after %.2f instalments.',
                $this->perInstallmentPct,
                $this->totalInstallments,
                $recovery,
                $recovery > 100.0
                    ? 'The borrower is scheduled to repay MORE than the capital advanced.'
                    : 'The borrower is scheduled to repay LESS than the capital advanced.',
                $implied
            );
        }

        return $warnings;
    }

    public function isCapitalRecoveryBalanced(): bool
    {
        return abs($this->scheduledCapitalRecoveryPct() - 100.0) <= 0.01;
    }
}
