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
    ];

    protected function casts(): array
    {
        return [
            'tts_enabled' => 'boolean',
            'smooth_animations' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
