<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientSetting extends Model
{
    /** @use HasFactory<\Database\Factories\PatientSettingFactory> */
    use HasFactory;

    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'tts_enabled',
        'smooth_animations',
        'stimulation_level',
        'default_routine_time_sec',
    ];

    protected function casts(): array
    {
        return [
            'tts_enabled' => 'boolean',
            'smooth_animations' => 'boolean',
            'default_routine_time_sec' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
