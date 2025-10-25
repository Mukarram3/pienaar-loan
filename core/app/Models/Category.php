<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use GlobalStatus;

    public function plans()
    {
        return $this->hasMany(LoanPlan::class);
    }
}
