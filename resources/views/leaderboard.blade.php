@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-3">Leaderboard</h1>

        @forelse ($leaders as $leader)
            <p>
                {{ $leader->name }}
                &mdash;
                {{ $leader->won_matches_count }}
            </p>
        @empty
            <em>Not enough matches for a leaderboard yet.</em>
        @endforelse
    </div>
@endsection
