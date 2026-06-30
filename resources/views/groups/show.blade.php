@extends('layouts.app')

@section('content')
<div class="group-container">   
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    <nav class="group-navbar" id="group-navbar">
        <a href="#description" class="nav-link active" onclick="showSection('description')">Description</a>
        <a href="#challenges" class="nav-link" onclick="showSection('challenges')">Challenges</a>
        <a href="#members" class="nav-link" onclick="showSection('members')">Members</a>
        @if (Auth::check() && ($owner->id == Auth::id() || Auth::user()->isAdmin()))
            <a href="#edit-info" class="nav-link" onclick="showSection('edit-info')">Edit Informations</a>
            <a href="{{ route('groups.requests', $group->id) }}" class="nav-link">Group Requests</a>
        @endif
    </nav>
    <div class="header-group-container">
        <a href="{{ route('groups.index') }}" class="go-back-button" id="group-go-back">Go Back</a>
        <h1 class="group-title">
            @if (!$group->public)
                <i class="fas fa-lock lock-icon"></i>
            @endif
            {{ $group->name }}
            <div id="badge">
            @if (Auth::check())
                @if ($owner->id == Auth::id())
                    <span class="badge badge-primary">Owner</span>
                @elseif ($members->contains('id', Auth::id()))
                    <span class="badge badge-secondary">Already In</span>
                @endif
            @endif
            </div>
        </h1>
        <div class="spacer"></div>
        <div class="group-buttons">
            @if (Auth::check() && ($members->contains('id', Auth::id()) || Auth::user()->isAdmin()))
                <a href="{{ route('groups.messages', $group->id) }}" class="group-chat-button">
                    <i class="fas fa-comments"></i> Group Chat
                </a>
            @endif
            @if (Auth::check() && $owner->id == Auth::id())
                <a href="{{ route('groups.createChallenge', $group->id) }}" class="create-challenge-button">
                    <i class="fas fa-plus"></i> Create Challenge
                </a>
            @endif
        @auth
            @if(Auth::check() && !$members->contains('id', Auth::id()) && $owner->id != Auth::id())
            
                @if($group->public || Auth::user()->isAdmin())
                    <form action="{{ route('groups.join', $group->id) }}" method="POST" class="join-button">
                        @csrf
                        <button type="submit" class="join-group-button">Join Group</button>
                    </form>
                @else
                    @php 
                        $groupRequest= App\Models\GroupRequest::where('id_user', Auth::id())->where('id_group', $group->id)->first();
                    @endphp

                    @if($groupRequest)
                        <form action="{{ route('groups.removeRequest', ['groupId'=>$group->id, 'userId' => Auth::id()]) }}" method="POST" class="join-button">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="join-group-button">Cancel Request</button>
                        </form>
                    @else
                        <form action="{{ route('groups.requestJoin', $group->id) }}" method="POST" class="join-button">
                            @csrf
                            <button type="submit" class="join-group-button">Request to Join</button>
                        </form>
                    @endif
                @endif
            @else
            <form action="{{ route('groups.leave', $group->id) }}" method="POST" class="join-button">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="join-group-button"><div>Leave Group</div></button>
                </form>
            @endif
        @else
            <form action="{{ route('login') }}" class="join-button" >
                @csrf
                <button type="submit" class="join-group-button">Request to Join</button>
            </form>
        @endauth

        @php 
            $owner=DB::table('group_owner')
            ->join('users', 'group_owner.id_user', '=', 'users.id')
            ->where('id_group', $group->id)->first();
        @endphp 
        @auth
            <a href="{{ route('report.create', ['type' => 'group','otherId'=>$owner->id]) }}" class="btn btn-danger">Report</a>
        @else
            <form action="{{ route('login') }}" >
                @csrf
                <button type="submit" class="btn btn-danger">Report</button>
            </form>
        @endauth
        @if (Auth::check() && (Auth::user()->isAdmin() || $owner->id == Auth::id()))
        <form action="{{ route('groups.destroy', $group->id) }}" method="POST" class="delete-group">
            @csrf
            @method('DELETE')
            <button type="submit" class="delete-group-button" onclick="return confirm('Are you sure you want to delete this group?')">Delete Group</button>
        </form>
        @endif
        </div>
    </div>
        </div>
    </div>
    <div id="description-section" class="section">
        @php
        $groupImage = asset('storage/images/group_images/' . $group_image);
        if ($group_image == 'default_group.png') {
            $groupImage = asset('images/default_group.png');
        }
        @endphp
        <img src="{{ $groupImage }}" alt="Group Image" class="group-image">
        <div class="group-text">
        <h2 class="group-description">{{ $group->description }}</h2>
        
        <footer class="created-data-group">
            Created on: {{ $group->creation_date }}
        </footer>
        </div>
    </div>


    <div id="challenges-section" class="section" style="display: none;">
        <div class="container">
            <div class="challenges">
                @php 
                    $challenges = DB::table('group_challenge')
                        ->join('challenge', 'challenge.id', '=', 'group_challenge.id_challenge')
                        ->where('group_challenge.id_group', $group->id)
                        ->get();
                @endphp
                @if ($challenges->isNotEmpty())
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
                        
                                <p>Created on: {{ $challenge->begin_date }}</p>
                                <p>Ends on: {{ $challenge->end_date }}</p>
                                @php
                                    $participating = DB::table('challenge_participants')
                                        ->where('id_challenge', $challenge->id)
                                        ->where('id_user', Auth::id())
                                        ->exists();
                                @endphp
                                @if ($participating)
                                    <p>You are participating in this challenge</p>
                                @else
                                    @php
                                        $member=DB::table('group_member')->where('id_user', Auth::id())->where('id_group', $group->id)->first();
                                    @endphp
                                    @if($challenge->private)
                                        @if($member)
                                            <form action="{{ route('challenges.joinchallenge', $challenge->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="participate-button">Participate</button>
                                            </form>
                                        @else
                                            <p> You need to be a member of the group to participate in this challenge. </p>
                                            <form action="{{ route('groups.join', $group->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="participate-button">Join Group</button>
                                            </form>
                                        @endif
                                    @else
                                        <form action="{{ route('challenges.joinchallenge', $challenge->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="participate-button">Participate</button>
                                        </form>
                                    @endif
                                @endif
                            </a>
                        </div>
                    @endforeach
                @else
                    <p> No challenges associated to this group. </p>
                    @if (Auth::check() && $owner->id == Auth::id())
                        <a href="{{ route('groups.createChallenge', $group->id) }}" class="create-challenge-button">
                            <i class="fas fa-plus"></i> Create Challenge
                        </a>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <div id="members-section" class="section" style="display: none;">
        <h3 class="members-title">Members</h3>
        @auth
            @if($group->public || Auth::user()->isAdmin() || $members->contains('id', Auth::id()))
                <p class="num-participants-group">{{ $group->num_participants }} / {{ $group->max_participants }} members</p>
                <table class="members-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($owner)
                            <tr>
                                <td>
                                    @if ($owner->id == Auth::id())
                                        <a href="{{ route('profile.show', $owner->id) }}">{{ '@' . $owner->username }}</a>
                                    @else
                                        <a href="{{ route('profile.showByUsername', $owner->username) }}">{{ '@' . $owner->username }}</a>
                                    @endif
                                </td>
                                <td><span class="owner-tag">Owner</span></td>
                            </tr>
                        @endif
                        @foreach ($members as $member)
                            @if ($owner && $member->username !== $owner->username)
                                <tr>
                                    <td>
                                        @if ($member->id == Auth::id())
                                            <a href="{{ route('profile.show', $member->id) }}">{{ '@' . $member->username }}</a>
                                        @else
                                            <a href="{{ route('profile.showByUsername', $member->username) }}">{{ '@' . $member->username }}</a>
                                        @endif
                                    </td>
                                    <td>Member</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>You don't belong to this private group.</p>
            @endif
        @else
            <p>You don't belong to this private group.</p>
        @endauth
        @if (Auth::check() && ($owner->id == Auth::id() || Auth::user()->isAdmin()))
            <div class="text-center">
                <a href="#manage-members" class="btn btn-primary" onclick="showSection('manage-members')">Manage</a>
            </div>
        @endif
    </div>

    <div id="edit-info-section" class="section" style="display: none;">
        <form id="edit-form"action="{{ route('groups.update', $group->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="edit-group-field">
                <label for="group_name" class="group-info-edit">Group Name:</label>
                <input type="text" name="group_name" id="group_name" class="group-edit-box" value="{{ $group->name }}">
            </div>
            <div class="edit-group-field">
                <label for="group_description" class="group-info-edit">Group Description:</label>
                <textarea name="group_description" id="group_description" class="group-edit-box">{{ $group->description }}</textarea>
            </div>
            <div class="edit-group-field">
                <label for="public" class="group-info-edit">Public:</label>
                <select name="public" id="public" class="group-edit-box">
                    <option value="1" {{ $group->public ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ !$group->public ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div class="edit-group-field">
                <label for="group_image" class="group-info-edit">Upload New Image:</label>
                <input type="file" name="group_image" id="group_image" class="group-edit-box">
            </div>
            <button type="submit" class="save-changes-button">Save Changes</button>
        </form>

    </div>

    <div id="manage-members-section" class="section" style="display: none;">
            <h3 class="members-title">Manage Members</h3>
            <table class="members-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($owner)
                        <tr>
                            <td>{{ '@' . $owner->username }}</td>
                            <td><span class="owner-tag">Owner</span></td>
                            <td></td>
                        </tr>
                    @endif
                    @foreach ($members as $member)
                        @if ($owner && $member->username !== $owner->username)
                            <tr>
                                <td>{{ '@' . $member->username }}</td>
                                <td>Member</td>
                                <td>
                                    <form action="{{ route('groups.removeMember', ['id' => $group->id, 'userId' => $member->id]) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="remove-member-button" onclick="return confirm('Are you sure you want to remove {{ $member->username }} from the group?')">X</button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            <div class="add-member-container">
                <label for="add-member-input">Invite Member</label>
                <div class="add-member-input-container">
                    <form action="{{ route('groups.inviteMember', $group->id) }}" method="POST" style="display: inline;" id="invite-member-form">
                        @csrf
                        <input id="add-member-input" type="text" name="username" placeholder="Enter Username" class="add-member-input" required>
                        <button type="submit" class="add-member-button" onclick="return confirm('Are you sure you want to invite this user to the group?')">Invite</button>
                        <div id="invite-dropdown" class="add-member-dropdown"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showSection(sectionId) {
            // Esconder todas as seções
            document.querySelectorAll('.section').forEach(section => {
                section.style.display = 'none';
            });
            left
            // Mostrar a seção selecionada
            document.getElementById(sectionId + '-section').style.display = 'block';

            // Atualizar a classe ativa na navbar
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            document.querySelector(`a[href="#${sectionId}"]`).classList.add('active');
        }
    </script>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
</div>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection
