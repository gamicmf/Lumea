
@extends('layouts.app')

@section('content')
    <div class="follow">
        <h1> Following</h1>
            <div class="followers-container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($following as $follower)
                    <div class="follower-card bg-white shadow-md rounded-lg border border-gray-200 p-4">
                        <!-- Emitter's Profile Picture -->
                        @php
                            $profilePicture = 'storage/profile_pictures/' . Auth::user()->profile_picture;
                            $defaultPicture = 'images/default.png';
                        @endphp
                        @if(Auth::user()==$follower)
                            <a href="{{ route('profile.show') }}">
                        @else
                            <a href="{{ route('profile.showByUsername', $follower->username) }}">
                        @endif
                            <img src="{{ asset(file_exists(public_path($profilePicture)) ? $profilePicture : $defaultPicture) }}" alt="Profile Picture" class="profile-picture">                        
                        </a>
                        <p class="text-gray-800 font-semibold text-lg">@. {{ $follower->username }}</p>
                        <form action="{{ route('profile.unfollow',['user_following'=>Auth::id(),'user_followed'=>$follower->id] ) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="mt-4 bg-red-500 text-white py-1 px-4 rounded hover:bg-red-600">
                                Unfollow
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-gray-500">No one following anyone .</p>
                @endforelse
            </div>
    </div>
@endsection
@section('footer')
    @include('layouts.footer')
@endsection