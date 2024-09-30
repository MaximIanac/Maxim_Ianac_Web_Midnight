<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'link',
        'genres',
        'release_date',
        'publisher',
        'reviews',
        'rating',
        'price',
        'positions',
    ];

    protected $casts = [
        'positions' => 'array',
        'release_date' => 'date',
    ];
}
