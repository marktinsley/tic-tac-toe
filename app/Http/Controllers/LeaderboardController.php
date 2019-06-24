<?php

namespace App\Http\Controllers;

use App\ComputerPlayer;
use App\User;

class LeaderboardController extends Controller
{
    public function index()
    {
        return view('leaderboard', [
            'leaders' => User::has('wonMatches')
                ->withCount('wonMatches')
                ->where('email', '!=', ComputerPlayer::EMAIL)
                ->get()
                ->sortByDesc('won_matches_count'),
        ]);
    }
}
