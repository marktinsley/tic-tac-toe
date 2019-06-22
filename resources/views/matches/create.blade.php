@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-5">Start a new match</h1>

        <form action="{{ route('matches.store') }}" method="post">
            @csrf

            <button class="btn btn-primary" style="width: 200px; height: 80px; font-size: 1.3rem;">
                Vs Computer
            </button>
        </form>
    </div>
@endsection
