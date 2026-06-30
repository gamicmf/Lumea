@extends('layouts.app')

@section('content')
<div id="groups-index-container" >
    <div class="header-challenge-container">
        <h1>Groups</h1>
        
    </div>

    <div class="header-container">
        <input type="text" id="search-groups" placeholder="Search groups">
        <div class="filter-container">
            <span id="filter-toggle" class="filter-toggle">
                <i class="fas fa-filter"></i> Filter by
            </span>
            <div id="filter-options" class="filter-options" style="display: none;">
                <div class="filter-option" data-value="all">All</div>
                <div class="filter-option" data-value="already-in">Already In</div>
                <div class="filter-option" data-value="not-in">Not Already In</div>
            </div>
        </div>
    </div>
    
    <div class="header-container">
        @auth
            <a class="button new-button" href="{{ route('groups.create') }}">Create a new group</a>
        @else
            <a class="button new-button" href="{{ route('login') }}">Login to create a new group</a>
        @endauth
    </div>
    

    <!-- Exibir grupos filtrados -->
    <div class="groups-container">
        @foreach ($groups as $group)
            <div class="group-card" data-group-name="{{ strtolower($group->name) }}" data-group-status="{{ $group->is_member ? 'already-in' : 'not-in' }}">
                <h2>
                    @if (!$group->public)
                        <i class="fas fa-lock lock-in"></i>
                    @endif
                    {{ $group->name }}
                </h2>
                <p>{{ $group->description }}</p>
                <p>Participants: {{ $group->num_participants }}</p>
                <p>Created on: {{ $group->creation_date }}</p>
                @if (Auth::check())
                    @if ($group->owner && $group->owner->id == Auth::id())
                        <p class="badge badge-primary">Owner</p>
                    @elseif ($group->members->contains(Auth::id()))
                        <p class="badge badge-secondary">Already In</p>
                    @endif
                @endif
                <a href="{{ route('groups.show', $group->id) }}" class="view-more-group" id="view-more-group">View More ></a>
            </div>
        @endforeach
    </div>
</div>

@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@section('scripts')
    <script src="{{ asset('js/group_search.js') }}"></script>
    <script src="{{ asset('js/group_filter.js') }}"></script>
@endsection
