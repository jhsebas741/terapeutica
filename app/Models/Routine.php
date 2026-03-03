<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{
    /** @use HasFactory<\Database\Factories\RoutineFactory> */
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function routineSteps()
    {
        return $this->hasMany(RoutineStep::class)->orderBy('order');
    }
}
