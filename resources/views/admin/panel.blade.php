@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Admin Panel</h1>
        @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif

        <div class="admin-sections">
            <div class="admin-section">
                <h2>Reports</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>User reported</th>
                            <th>Actions</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td>{{ $report->id }}</td>
                                <td>{{ $report->description }}</td>
                                <td>{{ $report->reportable_type }}</td>
                                <td>
                                    @if($report->reportable_type==='user')
                                        @php
                                            $owner = App\Models\User::find($report->reportable_id);
                                        @endphp
                                        <a href="{{ route('profile.showByUsername', $owner->username) }}">
                                            <p class="text-gray-800 font-semibold text-lg">@. {{ $owner->username }}</p>
                                        </a>
                                    @elseif($report->reportable_type==='post')
                                        @php
                                            $pub = App\Models\Publication::findOrFail($report->reportable_id);
                                            $post= App\Models\Post::find($pub->id_post);
                                            $owner = App\Models\User::find($post->id_poster);
                                        @endphp
                                        <a href="{{ route('profile.showByUsername', $owner->username) }}">
                                            <p class="text-gray-800 font-semibold text-lg">@. {{ $owner->username }}</p>
                                        </a>
                                    @elseif($report->reportable_type==='group')
                                        @php
                                            $owner = App\Models\User::find($report->reportable_id);
                                        @endphp
                                        <a href="{{ route('profile.showByUsername', $owner->username) }}">
                                            <p class="text-gray-800 font-semibold text-lg">@. {{ $owner->username }}</p>
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <!-- Actions for reports -->
                                     
                                    
                                    @if($report->reportable_type==='user')
                                        
                                        <form action="{{ route('admin.blockUser', $report->reportable_id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to block this user?')">Block</button>
                                        </form>
                                        
                                    @elseif($report->reportable_type==='post')
                                        <form action="{{ route('admin.blockUser', $report->reportable_id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to block this user?')">Block</button>
                                        </form>
                                        <form action="{{ route('publications.destroy', $report->reportable_id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this publication?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete Publication</button>
                                        </form>
                                        @php
                                            $pub = App\Models\Publication::find($report->reportable_id);
                                        @endphp
                                        <a href="{{ route('publications.show', $pub->id) }}" class="create-challenge-button">
                                            See publictaion
                                        </a>
                                       
                                    @else
                                        @php
                                            $owner = App\Models\User::find($report->reportable_id);
                                            $idGroup=DB::table('group_owner')->where('id_user',$owner->id)->first();
                                            $group = App\Models\Group::find($idGroup->id_group);
                                        @endphp
                                        <form action="{{ route('groups.destroy', $group->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this publication?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete Group</button>
                                        </form>   
                                        <a href="{{ route('groups.show', $group->id) }}" class="create-challenge-button">
                                            See group
                                        </a>
                                    @endif

                                    <form action="{{ route('reports.destroy', $report->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this report?')">
                                                &#10060;
                                            </button>
                                    </form>
                                    
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="admin-section">
                <h2>Users</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    @if (Auth::user()->id == $user->id)
                                        <a href="{{ route('profile.show', $user->id) }}">{{ $user->username }}</a>
                                    @else
                                        <a href="{{ route('profile.showByUsername', $user->username) }}">{{ $user->username }}</a>
                                    @endif
                                </td>
                                <td>{{ $user->isAdmin() ? 'Admin' : 'Member' }}</td>
                                <td>
                                    <form action="{{ route('admin.deleteUser', $user->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                    </form>
                                    @if (!$user->blocked)
                                        <form action="{{ route('admin.blockUser', $user->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to block {{ $user->username }}?')">Block</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.unblockUser', $user->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to unblock {{ $user->username }}?')">Unblock</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.promoteUser', $user->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to promote {{ $user->username }} to administrator?')">Promote</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="admin-section">
                <h2>Groups</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($groups as $group)
                            <tr>
                                <td>{{ $group->id }}</td>
                                <td><a href="{{ route('groups.show', $group->id) }}">{{ $group->name }}</a></td>
                                <td>{{ $group->description }}</td>
                                <td>
                                    <form action="{{ route('admin.deleteGroup', $group->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this group?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="admin-section">
                <h2>Challenges</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($challenges as $challenge)
                            <tr>
                                <td>{{ $challenge->id }}</td>
                                <td><a href="{{ route('challenges.show', $challenge->id) }}">{{ $challenge->name }}</a></td>
                                <td>{{ $challenge->description }}</td>
                                <td>
                                    <form action="{{ route('admin.deleteChallenge', $challenge->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this challenge?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="admin-section">
                <h2>FAQ Questions</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Question</th>
                            <th>User</th>
                            <th>Answered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($faqs as $faq)
                            <tr>
                                <td>{{ $faq->id }}</td>
                                <td>{{ $faq->question }}</td>
                                <td>
                                    @if (Auth::user()->id == $faq->user->id)
                                        <a href="{{ route('profile.show', $faq->user->id) }}">{{ $faq->user->username }}</a>
                                    @else
                                        <a href="{{ route('profile.showByUsername', $faq->user->username) }}">{{ $faq->user->username }}</a>
                                    @endif
                                </td>
                                <td>{{ $faq->answer ? 'Yes' : 'No' }}</td>
                                <td>
                                    @if (!$faq->answer)
                                        <a href="{{ route('admin.showAnswerFaqForm', $faq->id) }}" class="btn btn-primary">Answer</a>
                                    @else
                                        <a href="{{ route('admin.viewFaq', $faq->id) }}" class="btn btn-info">View Answer</a>
                                    @endif
                                    <form action="{{ route('admin.deleteFaq', $faq->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this FAQ?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection