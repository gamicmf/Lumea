<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home - Lumea</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>
    @extends('layouts.app')
    @section('content')
    <div class="banner">
        <div class="banner-text">
            <h1>Welcome to Lumea Community</h1>
            <p>Join us and explore publications, groups, and challenges available on platform.</p>
        </div>
    </div>
    <div class="container"id="home-container">
        <section class="latest-publications">
            <h2>Latest Publications</h2>
            <div class="card-container">
                @foreach($latestPublications as $publication)
                    <div class="card">
                        <img src="{{ asset('images/' . $publication->pub_image) }}" alt="Publication Image" class="publication-image">
                        <h3>{{ $publication->title }}</h3>
                        <p>{{ Str::limit($publication->content, 100) }}</p>
                        @php
                            $post=\App\Models\Post::findOrFail($publication->id_post);
                            $user=\App\Models\User::findOrFail($post->id_poster);
                        @endphp
                        @if($user)
                            <p>By: 
                                <a href="{{ route('profile.showByUsername', $user->username) }}" class="username-link">
                                    {{ $user->username }}
                                </a>
                            </p>
                        @else
                            <p>By: Unknown</p>
                        @endif
                        <a class="read-more-button" href="{{ route('publications.show', $publication->id) }}">See More</a>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="top-groups">
            <h2>Top Groups</h2>
            <div class="card-container">
                @foreach($topGroups as $group)
                    @if($group->public || Auth::check())
                        <div class="card">
                            <h3>
                                {{ $group->name }}
                                @if(!$group->public)
                                    <i class="fas fa-lock"></i>
                                @endif
                            </h3>
                            <p>{{ $group->description }}</p>
                            <a class="read-more-button"href="{{ route('groups.show', $group->id) }}">View Group</a>
                        </div>
                    @endif
                @endforeach
            </div>
        </section>

        <section class="top-challenges">
            <h2>Top Challenges</h2>
            <div class="card-container">
                @foreach($topChallenges as $challenge)
                    <div class="card">
                        <h3>{{ $challenge->name }}</h3>
                        <p>{{ $challenge->description }}</p>
                        <a class="read-more-button" href="{{ route('challenges.show', $challenge->id) }}">View Challenge</a>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    @include('layouts.footer')
    @endsection
</body>
</html>