<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanSettlement extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'final_settlement_date'  => 'datetime',
        'closure_effective_date' => 'datetime',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
