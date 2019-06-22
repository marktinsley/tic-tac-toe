@extends('layouts.app')

@section('content')
    <div class="container">
        <tic-tac-toe-match
                :match-id="{{ $match->id }}"
                :playerx-id="{{ $match->player1_id }}"
                :moves-made="{{ $match->moves }}"
        ></tic-tac-toe-match>
    </div>
@endsection
