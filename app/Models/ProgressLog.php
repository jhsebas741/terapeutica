<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressLog extends Model
{
    /** @use HasFactory<\Database\Factories\ProgressLogFactory> */
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'activity_type',
        'reference_id',
        'result',
        'score',
        'attempts',
        'time_spent_sec',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'attempts' => 'integer',
            'time_spent_sec' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}
