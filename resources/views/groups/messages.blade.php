@extends('layouts.app')

@section('content')
<a href="{{ route('groups.show', $group->id) }}" class="go-back-messages-button">Go Back</a>

<div class="container">
    <h1 class="message-title-chat">Group Chat for {{ $group->name }}</h1>
    <div class="chat-container">
        <div class="messages">
            @foreach($messages as $message)
                <div class="message">
                    @if($message->user)
                    @php
                        $profilePicture = 'storage/profile_pictures/' . Auth::user()->profile_picture;
                        $defaultPicture = 'images/default.png';
                    @endphp
                    <img src="{{ asset(file_exists(public_path($profilePicture)) ? $profilePicture : $defaultPicture) }}" alt="Profile Picture" class="profile-picture-small">
                        <div class="message-content">
                            <a href="{{ $message->user->id == Auth::id() ? route('profile.show', $message->user->id) : route('profile.showByUsername', $message->user->username) }}">
                                {{ '@' . $message->user->username }}
                            </a>
                            <span class="message-time">{{ $message->created_at->diffForHumans() }}</span>
                            <p>{{ $message->content }}</p>
                            @if(Auth::check() && ($message->user->id == Auth::id() || (isset($group->owner) && $group->owner && $group->owner->id == Auth::id()) || Auth::user()->isAdmin()))
                                <form action="{{ route('groups.deleteMessage', $message->id) }}" method="POST" class="delete-message-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-message-button">Delete</button>
                                </form>
                            @endif
                        </div>
                    @else
                        <div class="message-content">
                            <p>{{ $message->content }}</p>
                            <span class="message-time">{{ $message->created_at->diffForHumans() }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        <form action="{{ route('groups.sendMessage', $group->id) }}" method="POST" class="send-message-form">
            @csrf
            <textarea name="content" placeholder="Type your message here..." required></textarea>
            <button type="submit" class="send-message-button">Send</button>
        </form>
    </div>
</div>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection