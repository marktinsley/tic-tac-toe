@extends('layouts.app')

@section('content')
    <div class="container">
        <tic-tac-toe-match :match-id="{{ $match->id }}"></tic-tac-toe-match>
    </div>
@endsection
