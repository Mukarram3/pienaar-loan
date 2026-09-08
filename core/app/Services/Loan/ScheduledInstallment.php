<?php

// =============================================================
// File: app/Services/Loan/ScheduledInstallment.php
// =============================================================
declare(strict_types=1);

namespace App\Services\Loan;

/**
 * One row of the contractual repayment schedule.
 *
 * $isFinal marks the instalment that absorbs any rounding residual, so that
 * the schedule sums exactly to the declared totals.
 */
final class ScheduledInstallment
{
    public function __construct(
        public readonly int    $number,
        public readonly float  $capital,
        public readonly float  $profit,
        public readonly float  $total,
        public readonly int    $dayOffset,
        public readonly ?string $dueDate,
        public readonly float  $capitalOutstandingAfter,
        public readonly bool   $isFinal = false,
    ) {
    }

    public function toArray(): array
    {
        return [
            'number'                    => $this->number,
            'capital'                   => $this->capital,
            'profit'                    => $this->profit,
            'total'                     => $this->total,
            'day_offset'                => $this->dayOffset,
            'due_date'                  => $this->dueDate,
            'capital_outstanding_after' => $this->capitalOutstandingAfter,
            'is_final'                  => $this->isFinal,
        ];
    }
}
