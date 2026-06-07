<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettlementPayment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function quote()
    {
        return $this->belongsTo(RedemptionQuote::class, 'quote_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(Admin::class, 'recorded_by');
    }

}
