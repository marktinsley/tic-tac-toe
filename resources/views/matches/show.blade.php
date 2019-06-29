@extends('layouts.app')

@section('content')
    <div class="container">
        <tic-tac-toe-match
            :match-id="{{ $match->id }}"
            :playerx-id="{{ $match->player1_id }}"
            winner-name="{{ $match->winner ? $match->winner->name : null }}"
            :moves-made="{{ $match->moves }}"
            :read-only="{{ $match->isParticipant(\Illuminate\Support\Facades\Auth::user()) ? 'false' : 'true' }}"
        ></tic-tac-toe-match>
    </div>
@endsection
