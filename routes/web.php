<?php

use App\Http\Controllers\GamesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GamesController::class, 'index'])->name('index');
Route::get('/{id}/history', [GamesController::class, 'showHistory'])->name('game.history');
