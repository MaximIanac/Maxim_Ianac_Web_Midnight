<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\History;
use Illuminate\Http\Request;

class GamesController extends Controller
{
    public function index()
    {
        $games = Game::all();
        $history = History::all();

        $games = $games->sortBy(function ($game) {
            return $game->positions['en-us'] !== '-' ? $game->positions['en-us'] : PHP_INT_MAX;
        });


        return view('index', compact(['games', 'history']));
    }

    public function showHistory($id)
    {
        $game = Game::findOrFail($id);
        $history = History::where('game_id', '=', $id)->get()->groupBy('region');

//        $history = json_decode($game->history, true);

        return view('history', compact('game', 'history'));
    }
}
