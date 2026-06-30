
@extends('layouts.app')

@section('content')
<a href="{{ route('challenges.index') }}" class="go-back-button">Go Back</a>
<div class="challenge-show-container">
    <div class="challenge-info">
        <div class="challenge-title">
            <h1>{{ $challenge->name }}</h1>
            @if (Auth::check() && (Auth::user()->id === $challenge->id_creator || Auth::user()->isAdmin()))
                <a href="{{ route('challenges.edit', $challenge->id) }}" class="edit-button">
                    <i class="fas fa-pencil-alt"></i>
                </a>
            @endif
        </div>
        <p class="remaining-time">
            Remaining time: {{ \Carbon\Carbon::parse($challenge->end_date)->diffForHumans() }}
        </p>
        <p>{{ $challenge->description }}</p>
    </div>
    <div class="challenge-actions">

        @php
            $groupChallenge = DB::table('group_challenge')->where('id_challenge', $challenge->id)->first();
            $groupId = $groupChallenge ? $groupChallenge->id_group : null;
            $group = $groupId ? \App\Models\Group::findOrFail($groupId) : null;
            $isParticipatingInChallenge = DB::table('challenge_participants')->where('id_challenge', $challenge->id)->where('id_user', Auth::id())->exists();
            $isParticipatingInGroup = $group ? DB::table('group_member')->where('id_group', $group->id)->where('id_user', Auth::id())->exists() : false;
        @endphp
        @if (Auth::check())

            @if($group)
                @if ($isParticipatingInGroup)
                    @if ($isParticipatingInChallenge)
                        <p class="info">You are already participated in this challenge.</p>
                    @else 
                        <a href="{{ route('publications.create', ['challenge_id' => $challenge->id]) }}" class="button" id="participate-button">
                            Participate in Challenge
                        </a>
                    @endif
                @else
                    <p class="info">This challenge belongs to {{ $group->name }}. You need to join the group to participate in this challenge.</p>
                    <a href="{{ route('groups.show', ['id' => $groupId]) }}" class="button" id="participate-button">
                        See Group
                    </a>
                @endif
            @elseif($challenge->private)
                @if ($isParticipatingInChallenge)
                    <p class="info">You are already participated in this challenge.</p>
                @else 
                    <a href="{{ route('publications.create', ['challenge_id' => $challenge->id]) }}" class="button" id="participate-button">
                        Participate in Challenge
                    </a>
                @endif
            @else
                @if ($isParticipatingInChallenge)
                    <p class="info">You are already participated in this challenge.</p>
                @else 
                    <a href="{{ route('publications.create', ['challenge_id' => $challenge->id]) }}" class="button" id="participate-button">
                        Participate in Challenge
                    </a>
                @endif

            @endif

        @endif
    </div>

    <div class="publications-container">
        @foreach ($publications as $publication)
            <div class="publication">
                @if ($publication->post && $publication->post->user && ($publication->post->user->id === Auth::id() || (Auth::check() && Auth::user()->isAdmin())))
                    <div class="publication-actions">
                        <a href="{{ route('publications.edit', $publication->id) }}" class="edit-button">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <form action="{{ route('publications.destroy', $publication->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-button">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                @endif
                <a href="{{ url('publications/show/' . $publication->id) }}" class="publication-link">
                    <img src="{{ asset('images/' . $publication->pub_image) }}" alt="Publication Image">
                    <div class="publication-content">
                        <p class="publication-description">{{ $publication->description }}</p>
                        <div class="publication-meta">
                            <span>Ranking: {{ number_format($publication->ranking, 1) }} / 5.0</span>
                            @if ($publication->post)
                                <span>Posted on: {{ $publication->created_date }}</span>
                            @else
                                <span>Posted on: Unknown Date</span>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection