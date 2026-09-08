<?php

// =============================================================
// File: app/Models/LoanPaymentAllocation.php
// =============================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * One immutable ledger entry recording how a single received payment was split
 * between the Capital Sum and interest/profit.
 *
 * Rows are append-only. A correction is a new entry, never an edit, so the
 * audit trail of what was applied and when survives intact.
 */
class LoanPaymentAllocation extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'amount_received'           => 'decimal:2',
        'scheduled_amount'          => 'decimal:2',
        'late_fee_portion'          => 'decimal:2',
        'allocatable_amount'        => 'decimal:2',
        'capital_allocated'         => 'decimal:2',
        'profit_allocated'          => 'decimal:2',
        'unallocated_excess'        => 'decimal:2',
        'capital_ratio_applied'     => 'decimal:4',
        'profit_ratio_applied'      => 'decimal:4',
        'capital_outstanding_after' => 'decimal:2',
        'profit_outstanding_after'  => 'decimal:2',
        'is_partial'                => 'boolean',
        'is_overpayment'            => 'boolean',
        'value_date'                => 'date',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }

    public function recorder()
    {
        return $this->belongsTo(Admin::class, 'recorded_by');
    }

    public function sourceLabel(): string
    {
        return match ($this->source) {
            'opening_balance'       => 'Opening Balance (at import)',
            'scheduled_installment' => 'Scheduled Instalment',
            'manual'                => 'Manual Entry',
            'settlement'            => 'Settlement Payment',
            default                 => ucfirst(str_replace('_', ' ', (string) $this->source)),
        };
    }
}
