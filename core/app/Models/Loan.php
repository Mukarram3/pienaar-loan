<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\ApiQuery;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use ApiQuery;

    protected $guarded = ['id'];

    protected $casts = [
        'due_notification_sent' => 'datetime',
        'approved_at'           => 'datetime',
        'application_form'      => 'object',

        'quote_accepted_at'       => 'datetime',
        'settled_at'              => 'datetime',
        'closed_at'               => 'datetime',
        'security_released_at'    => 'datetime',
        'original_loan_date'      => 'date',
        'next_installment_date'   => 'date',
        'penalties_last_run_at' => 'datetime',
    ];

    /* ========= Relations ========= */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(LoanPlan::class, 'plan_id', 'id');
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    public function dueInstallments()
    {
        return $this->hasMany(Installment::class)->whereNull('given_at')->whereDate('installment_date', '<', now()->format('Y-m-d'));
    }

    public function nextInstallment()
    {
        return $this->hasOne(Installment::class)->whereNull('given_at');
    }

    /*========= Scopes =========*/
    public function scopePending($query)
    {
        return $query->where('status', Status::LOAN_PENDING);
    }

    public function scopeIn_review($query)
    {
        return $query->where('status', Status::LOAN_IN_REVIEW);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', Status::LOAN_APPROVED);
    }

    public function scopeRunning($query)
    {
        return $query->where('status', Status::LOAN_RUNNING);
    }

    public function scopePaid($query)
    {
        return $query->where('status', Status::LOAN_PAID);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', Status::LOAN_REJECTED);
    }

//    public function scopeApproved($query)
//    {
//        return $query->where('status', '!=', Status::LOAN_REJECTED);
//    }

    public function scopeDue($query)
    {
        return $query->where('status', Status::LOAN_RUNNING)->whereHas('installments', function ($q) {
            $q->whereNull('given_at')->whereDate('installment_date', '<', now()->format('Y-m-d'));
        });
    }

    /* ========= Accessors ========= */
    public function statusBadge(): Attribute
    {
        return Attribute::make(get: function () {
            $badge = '';
            if ($this->status == Status::LOAN_PENDING) {
                $badge = createBadge('dark', 'Pending');
            } elseif ($this->status == Status::LOAN_RUNNING) {
                $badge = createBadge('primary', 'Running');
            } elseif ($this->status == Status::LOAN_PAID) {
                $badge = createBadge('success', 'Paid');
            } elseif ($this->status == Status::LOAN_IN_REVIEW) {
                $badge = createBadge('warning', 'In-Review');
            } elseif ($this->status == Status::LOAN_APPROVED) {
                $badge = createBadge('success', 'Approved');
            } else {
                $badge = createBadge('danger', 'Rejected');
            }
            return $badge;
        });
    }

    public function payableAmount(): Attribute
    {
        return Attribute::make(get: fn () => $this->per_installment * $this->total_installment);
    }

    public function paidAmount(): Attribute
    {
        return Attribute::make(get: fn () => $this->per_installment * $this->given_installment);
    }

    public function documents()
    {
        return $this->hasMany(LoanDocument::class);
    }

    public function originalAgreement()
    {
        return $this->hasOne(LoanDocument::class)->where('document_type', 'original_agreement');
    }

    /**
     * Override payable_amount to respect legacy override if set
     */
    public function legacyloanpayableAmount(): Attribute
    {
        return Attribute::make(get: function () {
            if ($this->is_legacy && $this->total_repayable_override) {
                return (float) $this->total_repayable_override;
            }
            return $this->per_installment * $this->total_installment;
        });
    }

    /**
     * Total outstanding incl. legacy late fees + other charges
     */
    public function totalOutstanding(): Attribute
    {
        return Attribute::make(get: function () {
            $payable     = (float) $this->payable_amount;
            $paid        = (float) $this->paid_amount;
            $loanBalance = max(0, $payable - $paid);

            return $loanBalance
                + (float) $this->penalties_outstanding
                + (float) $this->historical_late_fees
                + (float) $this->other_charges;
        });
    }

    public function capitalProfitAllocation(): Attribute
    {
        return Attribute::make(get: function () {
            if (!$this->is_legacy) {
                return null;
            }

            $capitalRatio = $this->plan ? (float) $this->plan->capital_ratio : 0.5;
            $profitRatio  = $this->plan ? (float) $this->plan->profit_ratio  : 0.5;

            // Safety: ensure sum is sensible
            if ($capitalRatio + $profitRatio == 0) {
                $capitalRatio = $profitRatio = 0.5;
            }

            $payable = (float) $this->payable_amount;
            $paid    = (float) $this->paid_amount;

            return [
                'total_capital'        => $payable * $capitalRatio,
                'total_profit'         => $payable * $profitRatio,
                'capital_repaid'       => $paid * $capitalRatio,
                'profit_received'      => $paid * $profitRatio,
                'capital_outstanding'  => max(0, ($payable - $paid) * $capitalRatio),
                'profit_outstanding'   => max(0, ($payable - $paid) * $profitRatio),
                'capital_ratio'        => $capitalRatio,
                'profit_ratio'         => $profitRatio,
            ];
        });
    }

    public function activeQuote()
    {
        return $this->belongsTo(RedemptionQuote::class, 'active_quote_id');
    }

    public function redemptionQuotes()
    {
        return $this->hasMany(RedemptionQuote::class);
    }

    public function settlementPayments()
    {
        return $this->hasMany(SettlementPayment::class);
    }

    public function lifecycleEvents()
    {
        return $this->hasMany(LoanLifecycleEvent::class)->latest();
    }

    public function lifecycleStageLabel(): Attribute
    {
        return Attribute::make(get: function () {
            return match ((int) $this->lifecycle_stage) {
                Status::LIFECYCLE_ACTIVE              => 'Active',
                Status::LIFECYCLE_REDEMPTION_OFFERED  => 'Redemption Offered',
                Status::LIFECYCLE_REDEMPTION_ACCEPTED => 'Redemption Pending Payment',
                Status::LIFECYCLE_SETTLED             => 'Settled',
                Status::LIFECYCLE_CLOSED              => 'Closed',
                Status::LIFECYCLE_SECURITY_RELEASED   => 'Security Released',
                default                               => 'Unknown',
            };
        });
    }

    public function lifecycleStageBadge(): Attribute
    {
        return Attribute::make(get: function () {
            $map = [
                Status::LIFECYCLE_ACTIVE              => ['primary', 'Active'],
                Status::LIFECYCLE_REDEMPTION_OFFERED  => ['warning', 'Redemption Offered'],
                Status::LIFECYCLE_REDEMPTION_ACCEPTED => ['info', 'Pending Payment'],
                Status::LIFECYCLE_SETTLED             => ['success', 'Settled'],
                Status::LIFECYCLE_CLOSED              => ['dark', 'Closed'],
                Status::LIFECYCLE_SECURITY_RELEASED   => ['secondary', 'Security Released'],
            ];
            [$class, $label] = $map[(int) $this->lifecycle_stage] ?? ['light', 'Unknown'];
            return createBadge($class, $label);
        });
    }

    public function canMakePayment(): bool
    {
        return in_array((int) $this->lifecycle_stage, [
            Status::LIFECYCLE_ACTIVE,
            Status::LIFECYCLE_REDEMPTION_OFFERED,
        ]);
    }

    public function canGenerateRedemptionQuote(): bool
    {
        return $this->lifecycle_stage == Status::LIFECYCLE_ACTIVE
            && in_array($this->status, [Status::LOAN_RUNNING, Status::LOAN_APPROVED]);
    }

    public function canAcceptQuote(): bool
    {
        return $this->lifecycle_stage == Status::LIFECYCLE_REDEMPTION_OFFERED
            && $this->activeQuote
            && !$this->activeQuote->isExpired();
    }

    public function canRecordSettlementPayment(): bool
    {
        return $this->lifecycle_stage == Status::LIFECYCLE_REDEMPTION_ACCEPTED;
    }

    public function canCloseAccount(): bool
    {
        return $this->lifecycle_stage == Status::LIFECYCLE_SETTLED;
    }

    public function canReleaseSecurity(): bool
    {
        return $this->lifecycle_stage == Status::LIFECYCLE_CLOSED;
    }

    public function penaltiesOutstanding(): Attribute
    {
        return Attribute::make(get: function () {
            return max(0, (float) $this->accrued_penalties - (float) $this->penalties_paid - (float) $this->penalties_waived);
        });
    }

    /* ========= Other Methods ========= */

    public function shortCodes()
    {
        return [
            "plan_name"              => $this->plan->name,
            "loan_number"            => $this->loan_number,
            "amount"                 => number_format($this->amount, 2, '.', ''),
            "per_installment"        => number_format($this->per_installment, 2, '.', ''),
            "payable_amount"         => number_format($this->payable_amount, 2, '.', ''),
            "installment_interval"   => $this->installment_interval,
            "delay_value"            => number_format($this->delay_value, 2, '.', ''),
            "charge_per_installment" => number_format($this->charge_per_installment, 2, '.', ''),
            "delay_charge"           => number_format($this->delay_charge, 2, '.', ''),
            "given_installment"      => $this->given_installment,
            "total_installment"      => $this->total_installment,
            "reason_of_rejection"    => $this->admin_feedback,
        ];
    }
}
