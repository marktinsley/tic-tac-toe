<?php

namespace App\Http\Controllers;

use App\Match;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('matches.index', ['matches' => Match::inProgress()->get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('matches.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $match = Match::create([
            'type_key' => Match::TYPE_VS_COMPUTER,
            'player1_id' => Auth::id(),
        ]);

        return redirect()->route('matches.show', $match);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Match $match
     * @return \Illuminate\Http\Response
     */
    public function show(Match $match)
    {
        return view('matches.show', compact('match'));
    }
}
