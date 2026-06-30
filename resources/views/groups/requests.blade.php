@extends('layouts.app')

@section('content')
@if (Auth::check() && (Auth::user()->isAdmin() || $owner->id == Auth::id()))
    <div class="members-section">
        <a href="{{ route('groups.show', $group->id) }}" class="go-back-button">Go Back</a>
        <h3 class="members-requests-title">Group Requests for {{ $group->name }}</h3>
        <div class="group-requests-section">
            <table class="members-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                        <tr>
                            <td>{{ $request->username }}</td>
                            <td>
                                <form action="{{ route('groups.acceptRequest', ['groupId' => $group->id, 'userId' => $request->id]) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('groups.removeRequest', ['groupId' => $group->id, 'userId' => $request->id]) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h3 class="members-requests-title">Invites Sent to Users</h3>
        <div class="group-invites-section">
            <table class="members-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invites as $invite)
                        <tr>
                            <td>{{ $invite->username }}</td>
                            <td>
                                <form action="{{ route('groups.removeInvite', ['groupId' => $group->id, 'userId' => $invite->id]) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="alert alert-danger">
        You do not have permission to access this page.
    </div>
@endif
@endsection

@section('footer')
    @include('layouts.footer')
@endsection