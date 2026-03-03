<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoutineStep extends Model
{
    /** @use HasFactory<\Database\Factories\RoutineStepFactory> */
    use HasFactory;

    protected $fillable = [
        'routine_id',
        'pictogram_id',
        'order',
        'estimated_time_sec',
    ];

    public function routine()
    {
        return $this->belongsTo(Routine::class);
    }

    public function pictogram()
    {
        return $this->belongsTo(Pictogram::class);
    }
}
