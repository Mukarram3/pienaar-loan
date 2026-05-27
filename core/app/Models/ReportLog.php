<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
