@extends('layouts.app')

@section('content')
<div class="container">
    <nav class="navbar">
        <a href="#profile" class="nav-link active" onclick="showSection('profile')">Profile</a>
        <a href="#notifications" class="nav-link" onclick="showSection('notifications')">Notifications</a>
        @if(Auth::check() && Auth::user()->isAdmin())
            <a href="#admin-notifications" class="nav-link" onclick="showSection('admin-notifications')">Admin Notifications</a>
        @endif
    </nav>
    <div id="profile-section" class="section" style="display:block;">
        <div class="total">
            <div class="profile">
                @php
                    $profilePicture = 'storage/profile_pictures/' . Auth::user()->profile_picture;
                    $defaultPicture = 'images/default.png';
                @endphp
                <img src="{{ asset(file_exists(public_path($profilePicture)) ? $profilePicture : $defaultPicture) }}" alt="Profile Picture" class="profile-picture">

                <p>{{ $user->name }}</p>
                <p> @ {{ Auth::user()->username }}</p>

                <div class="header-container">
                    <a class="button new-button edit-button-profile" href="{{ route('profile.mudar', Auth::user()->id) }}">Edit</a>
                </div>
                
            </div>
            <div class="followers">
                <div>
                    <span class="font-bold">
                        <a href="{{ route('profile.followers', Auth::user()->id) }}">Followers</a>
                    </span> {{ Auth::user()->countFollowers() }}
                </div>
                <div>
                    <span class="font-bold">
                        <a href="{{ route('profile.following', Auth::user()->id) }}">Following</a>
                    </span> {{ $user->countFollowing() }}
                </div>
            </div>
            <p class="text-gray-700 text-sm mt-4">
                {{ Auth::user()->description }}
            </p>
            <div>
                <img src="{{ asset('images/assets/graph.png') }}" alt="Graphic Creativity" class="profile-picture">
                <p>Creativity</p>

                <img src="{{ asset('images/assets/graph.png') }}" alt="Graphic Technique" class="profile-picture">
                <p>Technique</p>

                <img src="{{ asset('images/assets/graph.png') }}" alt="Graphic Aesthetic" class="profile-picture">
                <p>Aesthetic</p>
            </div>
            <p class="ranking"> Ranking: {{ Auth::user()-> ranking}} /30</p>

            <!-- Publications Section -->
            <div class="publications">
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
                    <p>
                        No publications yet.
                        Choose a challenge and start creating!
                    </p>
                    <a href="{{ route('challenges.index') }}" class="go-back-button"> Challenges</a>
                @endforelse
            </div>
        </div>
    </div>
</div>
    <div id="notifications-section" class="section hidden mt-6">

            <div class="notifications-container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($notifications as $notification)
                @if($notification->adminNotification)
                @else
                    <div class="notification-card bg-white shadow-md rounded-lg border border-gray-200 p-4 {{ $notification->viewed ? 'viewed' : '' }}" data-id="{{ $notification->id }}">
                    <!-- Emitter's Profile Picture -->
                        @php
                            $profilePicture = 'storage/profile_pictures/' . Auth::user()->profile_picture;
                            $defaultPicture = 'images/default.png';
                        @endphp
                        <img src="{{ asset(file_exists(public_path($profilePicture)) ? $profilePicture : $defaultPicture) }}" alt="Profile Picture" class="profile-picture">

                        @if ($notification->userNotification)

                            <div class="notification-content">
                                
                                @if ($notification->userNotification->notification_type == 'started_following')
                                <p class="text-gray-800 font-semibold text-lg">
                                    {{ $notification->emitter->name }} started following you.
                                </p>

                                @elseif($notification->userNotification->notification_type === 'request_follow')
                                   
                                    <p class="text-gray-800 font-semibold text-lg">
                                        {{ $notification->emitter->name }} requested to follow you.
                                    </p>
                                    <div class="flex justify-between mt-4">
                                        <form action="{{ route('follow.accept', ['id_follower' => $notification->emitter_user ,'id_followed' => $notification->received_user, 'notification_id' => $notification->id]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="button new-button">Accept</button>
                                        </form>
                                        <form action="{{ route('follow.reject',['id_follower' => $notification->emitter_user ,'id_followed' => $notification->received_user, 'notification_id' => $notification->id]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="button new-button">Reject</button>
                                        </form>
                                    </div>
                                @elseif($notification->userNotification->notification_type === 'accepted_follow')
                                    <p class="text-gray-800 font-semibold text-lg">
                                        {{ $notification->emitter->name }} started following you.
                                    </p>
                                @elseif($notification->userNotification->notification_type === 'promoved_admin')
                                    <p class="text-gray-800 font-semibold text-lg">
                                        You have been promoted to Administrator of Lumea website.
                                    </p>   
                                @endif
                            </div>
                        @elseif($notification->commentNotification)
                            @php
                                $idcomment= $notification->commentNotification->id_comment;
                                $commentaire= \App\Models\Comment::find($idcomment);
                                $user_emmiter= \App\Models\User::find($notification->emitter->id);
                            @endphp

                            @if($notification->commentNotification->notification_type ==='comment_publication')
                                <div class="notification-content">
                                    <p class="text-gray-800 font-semibold text-lg">
                                        @ {{ $user_emmiter->username }}  commented on your publication: 
                                    </p>
                                    <p class="text-gray-500 mt-4">
                                            {{ $commentaire->comment_text }}
                                    </p>
                                    <a href="{{ route('publications.show', $commentaire->id_publication) }}" class="text-blue-500 mt-4">
                                        View Publication
                                    </a>
                                </div>
                            @elseif($notification->commentNotification->notification_type ==='reply_comment')
                                <div class="notification-content">
                                    <p class="text-gray-800 font-semibold text-lg">
                                        @ {{ $user_emmiter->username }}  replied to your comment: 
                                    </p>
                                    <p class="text-gray-500 mt-4">
                                            {{ $commentaire->comment_text }}
                                    </p>
                                    <a href="{{ route('publications.show', $commentaire->id_publication) }}" class="text-blue-500 mt-4">
                                        View Publication
                                    </a>
                                </div>
                            @elseif($notification->commentNotification->notification_type ==='liked_comment')
                                <div class="notification-content">
                                    <p class="text-gray-800 font-semibold text-lg">
                                        @ {{ $user_emmiter->username }}  liked to your comment: 
                                    </p>
                                    <p class="text-gray-500 mt-4">
                                            {{ $commentaire->comment_text }}
                                    </p>
                                    <a href="{{ route('publications.show', $commentaire->id_publication) }}" class="text-blue-500 mt-4">
                                        View Publication
                                    </a>
                                </div>
                             @elseif($notification->commentNotification->notification_type ==='deleted_comment')
                                <div class="notification-content">
                                        <p class="text-gray-800 font-semibold text-lg">
                                            Your comment has been deleted.
                                        </p>
                                        <a href="{{ route('publications.show', $commentaire->id_publication) }}" class="text-blue-500 mt-4">
                                            View Publication
                                        </a>
                                    </div>
                            @endif
                        
                        @elseif($notification->publicationNotification)
                            @php
                                $user_emmiter= \App\Models\User::find($notification->emitter->id);
                            @endphp
                            @if($notification->publicationNotification->notification_type==='vote_post')
                                <div class="notification-content">
                                    <p class="text-gray-800 font-semibold text-lg">
                                        @ {{ $user_emmiter->username }}  voted on your publication: 
                                    </p>
                                    <a href="{{ route('publications.show', $notification->publicationNotification->id_publication) }}" class="text-blue-500 mt-4">
                                        View Publication
                                    </a>
                                </div>
                            @elseif($notification->publicationNotification->notification_type==='publication_post')
                                <div class="notification-content">
                                    <p class="text-gray-800 font-semibold text-lg">
                                        @ {{ $user_emmiter->username }} just posted. 
                                    </p>
                                    <a href="{{ route('publications.show', $notification->publicationNotification->id_publication) }}" class="text-blue-500 mt-4">
                                        View Publication
                                    </a>
                                </div>
                            @elseif($notification->publicationNotification->notification_type==='deleted_publication')
                                <div class="notification-content">
                                    <p class="text-gray-800 font-semibold text-lg">
                                        Your publication has been deleted.
                                    </p>
                                </div>

                            @endif
                        @elseif($notification->groupNotification)
                            @php
                                $group = \App\Models\Group::find($notification->groupNotification->id_group);
                            @endphp
                            @if($notification->groupNotification->notification_type === 'request_join')
                                <div class="notification-content">
                                    <p class="text-gray-800 font-semibold text-lg">
                                        {{ $notification->emitter->username }} have requested to join the group: {{ $group->name }}      
                                    </p>
                                    <a href="{{ route('groups.requests', $group->id) }}" class="text-blue-500">
                                        View Request
                                    </a>
                                </div>
                            @elseif($notification->groupNotification->notification_type === 'received_message')
                                <div class="notification-content">
                                    <p class="text-gray-800 font-semibold text-lg">
                                        {{ $notification->emitter->username }} sent you a message in the group: {{ $group->name }} 
                                    </p>
                                    <a href="{{ route('groups.messages', $group->id) }}" class="text-blue-500 mt-4">
                                        View Group
                                    </a>
                                </div>
                            @elseif($notification->groupNotification->notification_type === 'join_group')
                                <div class="notification-content">
                                    <p class="text-gray-800 font-semibold text-lg">
                                        {{ $notification->emitter->username }} join the group: {{ $group->name }} 
                                    </p>
                                    <a href="{{ route('groups.show', $group->id) }}" class="text-blue-500 mt-4">
                                        View Group
                                    </a>
                                </div>
                            @elseif($notification->groupNotification->notification_type === 'leave_group')
                                <div class="notification-content">
                                    <p class="text-gray-800 font-semibold text-lg">
                                        {{ $notification->emitter->username }} leave the group: {{ $group->name }} 
                                    </p>
                                    <a href="{{ route('groups.show', $group->id) }}" class="text-blue-500 mt-4">
                                        View Group
                                    </a>
                                </div>
                            @elseif($notification->groupNotification->notification_type === 'added_t_group')
                                <div class="notification-content">
                                    <p class="text-gray-800 font-semibold text-lg">
                                        You have been enter in the {{ $group->name }} group.
                                    </p>
                                    <a href="{{ route('groups.show', $group->id) }}" class="text-blue-500 mt-4">
                                        View Group
                                    </a>
                                </div>
                            @elseif($notification->groupNotification->notification_type === 'invite_member')
                                
                                <p class="text-gray-800 font-semibold text-lg">
                                    {{ $notification->emitter->name }} invited to the {{ $group->name }} group.
                                </p>
                                <div class="flex justify-between mt-4">
                                    <form action="{{ route('groups.acceptInvite', $group->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="button new-button">Accept</button>
                                    </form>
                                    <form action="{{ route('groups.removeInvite',['groupId' => $group->id ,'userId' => Auth::id()]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="button new-button">Reject</button>
                                    </form>
                                </div>
                            @elseif($notification->groupNotification->notification_type === 'deleted_group')
                                <div class="notification-content">
                                    <p class="text-gray-800 font-semibold text-lg">
                                        The {{ $group->name }} group has been deleted.
                                    </p>
                                </div>
                            @elseif($notification->groupNotification->notification_type === 'removed_f_group')
                                <div class="notification-content">
                                    <p class="text-gray-800 font-semibold text-lg">
                                        {{ $notification->received->username }} was removed from the group: {{ $group->name }} 
                                    </p>
                                    <a href="{{ route('groups.show', $group->id) }}" class="text-blue-500 mt-4">
                                        View Group
                                    </a>
                                </div>
                            @elseif($notification->groupNotification->notification_type === 'expelled_group')
                                <div class="notification-content">
                                    <p class="text-gray-800 font-semibold text-lg">
                                        {{ $notification->emitter->username }} removed you from the group: {{ $group->name }} 
                                    </p>
                                    <a href="{{ route('groups.show', $group->id) }}" class="text-blue-500 mt-4">
                                        View Group
                                    </a>
                                </div>
                             @elseif($notification->groupNotification->notification_type === 'created_challenge')
                                <div class="notification-content">
                                    <p class="text-gray-800 font-semibold text-lg">
                                        {{ $notification->emitter->username }} created a challenge for the group: {{ $group->name }} .
                                    </p>
                                    <a href="{{ route('groups.show', $group->id) }}" class="text-blue-500 mt-4">
                                        View Group
                                    </a>
                                </div>
                            
                            @endif

                        @endif    
                        <!-- Notification Timestamp -->
                        <div class="notification-meta text-gray-400 text-xs mt-4">
                            {{ $notification->date->diffForHumans() }}
                        </div>
                    </div>
                @endif
            @empty
                <p class="text-gray-500">No notifications yet.</p>
            @endforelse
         </div>
    </div>
    @if(Auth::check() && Auth::user()->isAdmin())
        <div id="admin-notifications-section" class="section hidden mt-6">
            <h2 class="text-xl font-bold mb-4">Admin Notifications</h2>
                <div class="notifications-container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($notifications as $notification)
                    @if($notification->adminNotification)
                        <div class="notification-card bg-white shadow-md rounded-lg border border-gray-200 p-4">
                            <!-- Emitter's Profile Picture -->
                            @php
                                $profilePicture = 'storage/profile_pictures/' . Auth::user()->profile_picture;
                                $defaultPicture = 'images/default.png';
                                $report= \App\Models\Report::find($notification->adminNotification->id_report);
                                $user_reported= \App\Models\User::find($report->reportable_id);
                            @endphp
                            <img src="{{ asset(file_exists(public_path($profilePicture)) ? $profilePicture : $defaultPicture) }}" alt="Profile Picture" class="profile-picture">
                                <div class="notification-content">
                                    <p class="text-gray-800 font-semibold text-lg">
                                        {{ $notification->emitter->name }}
                                    </p>
                                    @if ($notification->adminNotification->notification_type == 'report_user')
                                        <p class="text-gray-600">
                                        reported {{$user_reported->username}} account.
                                        </p>
                                   
                                    @elseif ($notification->adminNotification->notification_type == 'report_post')
                                        <p class="text-gray-600">
                                        reported a publication.
                                        </p>

                                    @elseif ($notification->adminNotification->notification_type == 'report_group')
                                        <p class="text-gray-600">
                                            reported a group.
                                        </p>
                                    @endif
                                    <a href="{{ route('admin.panel') }}" class="text-blue-500 mt-4">
                                        View Report
                                    </a>
                                </div>
                            <!-- Notification Timestamp -->
                            <div class="notification-meta text-gray-400 text-xs mt-4">
                                {{ $notification->date->diffForHumans() }}
                            </div>
                        </div>
                    @endif
                @empty
                    <p class="text-gray-500">No notifications yet.</p>
                @endforelse
        </div>
    @endif
@endsection

    <script>
            window.onload = function () {
                // Set the default section to be shown
                showSection('profile');
            };

            function showSection(sectionId) {
                // Hide all sections
                document.querySelectorAll('.section').forEach(section => {
                    section.style.display = 'none';
                });

                // Show the selected section
                document.getElementById(sectionId + '-section').style.display = 'block';

                // Update the active class in the navbar
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                });
                document.querySelector(`a[href="#${sectionId}"]`).classList.add('active');
            }
        </script>
      

@section('footer')
    @include('layouts.footer')
@endsection