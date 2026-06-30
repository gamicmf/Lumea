@if (Auth::id() === $user->id)
<script>
    window.location.href = "{{ route('profile.show', Auth::id()) }}";
    </script>
@else
@extends('layouts.app')

@section('content')
<div class="container">

    <div class="total">
        <div class="profile">
            @php
                $profilePicture = 'storage/profile_pictures/' . $user->profile_picture;
                $defaultPicture = 'images/default.png';
            @endphp
            <img src="{{ asset(file_exists(public_path($profilePicture)) ? $profilePicture : $defaultPicture) }}" alt="Profile Picture" class="profile-picture">

            <p>{{ $user->name }}</p>
            <p> @ {{ $user->username }}</p>
        </div>
        @if (Auth::check() && Auth::user()->isAdmin())
            <div class="header-container">
                <a class="button new-button" href="{{ route('profile.mudar', $user->id) }}">Edit</a>
            </div>
        @endif
        <div class="followers">
            <div>
                <span class="font-bold">
                    <a href="{{ route('profile.followers', $user->id) }}">Followers</a>
                </span> {{ $user->countFollowers() }}
            </div>
            <div>
                <span class="font-bold">
                    <a href="{{ route('profile.following', $user->id) }}">Following</a>
                </span> {{ $user->countFollowing() }}
            </div>
        </div>
        @php
            $followRequest = \App\Models\FollowRequest::where('id_follower', Auth::id())->where('id_followed', $user->id)->first()
        @endphp

        @if(Auth::user()->isFollowing($user))
            <form action="{{ route('profile.unfollow',['user_following'=>Auth::id(),'user_followed'=>$user->id] ) }}" method="POST">
                @csrf
                @method('DELETE')
                <button class="mt-4 bg-red-500 text-white py-1 px-4 rounded hover:bg-red-600">
                    Unfollow
                </button>
            </form>
        
        @elseif($followRequest)
            <form action="{{ route('follow_requests.destroy',['id_followed'=>$user->id,'id_follower'=>Auth::id()]) }}" method="POST">
                @csrf
                @method('DELETE')
                <button class="mt-4 bg-red-500 text-white py-1 px-4 rounded hover:bg-red-600">
                    Waiting
                </button>
            </form>
        @else
                <form action="{{ route('profile.follow', $user->id) }}" method="POST" onsubmit="console.log('Form submitted');">
                    @csrf
                    <button class="mt-4 bg-blue-500 text-white py-1 px-4 rounded hover:bg-blue-600">
                        Follow
                    </button>
                </form>

        @endif

        @if (Auth::check() && Auth::user()->id !== $user->id)
            <a href="{{ route('report.create', ['type' => 'user','otherId'=>$user->id]) }}" class="btn btn-danger">Report</a>
        @endif
        
        <p class="text-gray-700 text-sm mt-4">
            {{ $user->description }}
        </p>
        <div>
            <img src="{{ asset('images/assets/graph.png') }}" alt="Creativity Graph" class="profile-picture">
            <p>Creativity</p>

            <img src="{{ asset('images/assets/graph.png') }}" alt="Technique Graph" class="profile-picture">
            <p>Technique</p>

            <img src="{{ asset('images/assets/graph.png') }}" alt="Aesthetic Graph" class="profile-picture">
            <p>Aesthetic</p>
        </div>
            
        <p class="ranking"> Ranking: {{ $user->ranking }} / 30</p>

        <!-- Publications Section -->
        @if ($isPrivate && Auth::user()->id !== $user->id && Auth::check() && !Auth::user()->isAdmin())
            <p>This profile is private. Only the user or admins can see the publications.</p>
        @else
            <div class="user-publications mt-6">
                <h2 class="text-xl font-bold mb-4">Publications</h2>
                @forelse ($publications as $publication)
                <div class="publication">
                    @if ($publication->post && $publication->post->user && ($publication->post->user->id === Auth::id() ||(Auth::check() && Auth::user()->isAdmin())))
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
                @empty
                    <p>No publications yet.</p>
                @endforelse
            </div>
        @endif

        @if (Auth::check())
            @if (Auth::user()->id == $user->id || Auth::user()->isAdmin())
                <form action="{{ route('user.delete', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this account?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete-account">Delete Account</button>
                </form>
            @endif
        @else
            <p class="alert alert-warning">You need to login to this account</p>
        @endif

    </div>

</div>
    @endsection

    @section('footer')
        @include('layouts.footer')
    @endsection
@endif