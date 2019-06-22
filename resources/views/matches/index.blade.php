@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-3">Matches in Progress</h1>

        <ul>
            @foreach ($matches as $match)
                <li>
                    <a href="{{ route('matches.show', $match) }}">Match {{ $match->id }}</a>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
