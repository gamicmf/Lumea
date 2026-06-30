@extends('layouts.app')

@section('content')
<div id="challenges-index-container" class="container">
    <nav class="challenge-selection">
        <a href="{{ route('challenges.index') }}" class="{{ request()->is('challenges') ? 'active' : '' }}">All Challenges</a>
        <a href="{{ route('challenges.participating') }}" class="{{ request()->is('challenges/participating') ? 'active' : '' }}">Participating</a>
    </nav>
    <div class="challenges-container">
        @if ($challenges->isEmpty())
            <p>No challenges found.</p>
        @endif
        @foreach ($challenges as $challenge)
        <div class="challenge-card">
                <a  href="{{ route('challenges.show', $challenge->id) }}">
                    <h2>
                        @if ($challenge->private)
                            <i class="fas fa-lock lock-in"></i>
                        @endif
                        {{ $challenge->name }}
                    </h2>
                    <p>{{ $challenge->description }}</p>
                    <p>Participants: {{ $challenge->num_participants }}</p>
                    <p>Created on: {{ $challenge->creation_date }}</p>
                    <p>Ends on: {{ $challenge->end_date }}</p>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection