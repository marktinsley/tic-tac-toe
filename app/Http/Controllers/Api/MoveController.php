<?php

namespace App\Http\Controllers\Api;

use App\Match;
use App\MatchReferee;
use App\Tile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MoveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Match $match
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Match $match, Request $request)
    {
        $request->validate(['column' => 'required|string', 'row' => 'required|int']);

        $tile = new Tile($request->input('column'), $request->input('row'));
        return (new MatchReferee($match))->attemptMove(Auth::user(), $tile);
    }
}
