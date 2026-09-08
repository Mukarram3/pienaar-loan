<?php

// =============================================================
// File: app/Models/LoanPlan.php
// =============================================================
namespace App\Models;

use App\Traits\ApiQuery;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class LoanPlan extends Model
{
    use GlobalStatus, ApiQuery;

    protected $guarded = ['id'];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function delayCharge(): Attribute
    {
        return Attribute::make(get: fn () => $this->fixed_charge + ($this->per_installment * $this->percent_charge / 100));
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function loanInterest()
    {
        return ($this->total_installment * $this->per_installment) - 100;
    }

    public function scopeLegacy($query)
    {
        return $query->where('is_legacy', 1);
    }

    public function scopeStandard($query)
    {
        return $query->where('is_legacy', 0);
    }

    public const CALC_STANDARD      = 'standard';
    public const CALC_FIXED_CAPITAL = 'fixed_capital';

    /**
     * Does this plan use the Fixed Capital Repayment engine?
     *
     * When true, Per Instalment % is a CAPITAL rate and the instalment is
     * derived as capital / capital_ratio. When false the plan keeps its
     * existing behaviour untouched (spec s.24).
     */
    public function usesFixedCapital(): bool
    {
        return $this->calculation_type === self::CALC_FIXED_CAPITAL;
    }

    /**
     * Build the validated terms object for the calculation engine.
     * Throws InvalidArgumentException if the plan is misconfigured.
     */
    public function loanTerms(): \App\Services\Loan\LoanTerms
    {
        return \App\Services\Loan\LoanTerms::fromPlan([
            'per_installment'            => $this->per_installment,
            'capital_ratio'              => $this->capital_ratio,
            'profit_ratio'               => $this->profit_ratio,
            'total_installment'          => $this->total_installment,
            'installment_interval'       => $this->installment_interval,
            'application_fixed_charge'   => $this->application_fixed_charge,
            'application_percent_charge' => $this->application_percent_charge,
        ]);
    }

    /**
     * Authoritative figures for a given advance under this plan.
     *
     * THE single entry point used by the quotation screen, loan creation, the
     * API and the PDF, so they cannot disagree (spec s.20). Returns null for
     * plans that do not use the Fixed Capital engine, and null if the plan is
     * misconfigured, so callers fall back to existing behaviour rather than
     * showing an error to a borrower.
     */
    public function quoteFor(float $amount): ?\App\Services\Loan\LoanQuote
    {
        if (!$this->usesFixedCapital()) {
            return null;
        }

        try {
            return app(\App\Services\Loan\LoanCalculator::class)
                ->quote($amount, $this->loanTerms());
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error(
                'Loan plan ' . $this->id . ' is misconfigured: ' . $e->getMessage()
            );
            return null;
        }
    }

    public function scopeFixedCapital($query)
    {
        return $query->where('calculation_type', self::CALC_FIXED_CAPITAL);
    }
}
