@extends('layouts.app')

@section('content')
<div id="challenges-index-container" class="container">
    <nav class="challenge-selection">
        <a href="{{ route('challenges.index') }}" class="{{ request()->is('challenges') ? 'active' : '' }}">All Challenges</a>
        <a href="{{ route('challenges.participating') }}" class="{{ request()->is('challenges/participating') ? 'active' : '' }}">Participating</a>
    </nav>
    </nav>
    @if (Auth::check() && Auth::user()->isAdmin())
        <div class="header-container">
            <a href="{{ route('challenges.create') }}" class="button new-button">New</a>
        </div>
    @endif
    <div class="search-bar">
        <input type="text" id="search-challenges" placeholder="Search challenges...">
        <div id="loading-indicator" style="display: none;">Loading...</div>
    </div>

    <div id="challenges-container" class="challenges-container">
        @foreach ($challenges as $challenge)
            <div class="challenge-card">
                <a  href="{{ route('challenges.show', $challenge->id) }}">
                    <h2>
                        @if ($challenge->private)
                            <i class="fas fa-lock lock-in"></i>
                        @endif
                        {{ $challenge->name }}
                    </h2>
                    <div id="challenge-description">{{ $challenge->description }}</div>
                    <p>Participants: {{ $challenge->num_participants }}</p>
                    <p>Created on: {{ $challenge->creation_date }}</p>
                    <p>Ends on: {{ $challenge->end_date }}</p>
                    @if ($challenge->is_participating)
                        <p>You are participating in this challenge</p>
                    @endif
                </a>
            </div>
        @endforeach
    </div>
</div>
</div>
</div>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@section('scripts')
    <script src="{{ asset('js/challenges_search.js') }}"></script>
@endsection