<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameElement extends Model
{
    /** @use HasFactory<\Database\Factories\GameElementFactory> */
    use HasFactory;

    protected $fillable = [
        'game_id',
        'pictogram_id',
        'situation_text',
        'sequence_order',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function pictogram()
    {
        return $this->belongsTo(Pictogram::class);
    }
}
