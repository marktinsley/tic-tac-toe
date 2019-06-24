@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-3">Matches in Progress</h1>

        @forelse ($matches as $match)
            <p>
                <a href="{{ route('matches.show', $match) }}">Match {{ $match->id }}</a>
            </p>
        @empty
            <em>No matches running at this time.</em>
        @endforelse
    </div>
@endsection
