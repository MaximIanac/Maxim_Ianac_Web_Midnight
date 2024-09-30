<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $fillable = ['game_id', 'region', 'position', 'date'];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
