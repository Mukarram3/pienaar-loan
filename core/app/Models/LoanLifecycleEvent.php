<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanLifecycleEvent extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function actor()
    {
        return $this->belongsTo(Admin::class, 'actor_id');
    }

    public static function log(int $loanId, string $event, ?int $fromStage = null, ?int $toStage = null, ?string $notes = null, array $metadata = []): self
    {
        return self::create([
            'loan_id'    => $loanId,
            'actor_id'   => auth('admin')->id(),
            'actor_type' => 'admin',
            'event_type' => $event,
            'from_stage' => $fromStage,
            'to_stage'   => $toStage,
            'notes'      => $notes,
            'metadata'   => $metadata,
        ]);
    }
}
