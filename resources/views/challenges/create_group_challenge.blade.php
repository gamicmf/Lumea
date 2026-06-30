@extends('layouts.app')

@section('content')
<a href="{{ url()->previous() }}" class="go-back-button">Go Back</a>
    <h1>Create Challenge for:
        <p>{{ $group->name }}</p>
    </h1>

    <form method="POST" action="{{ route('groups.storeChallenge', ['group_id' => $group->id]) }}" enctype="multipart/form-data">
        @csrf

        <div>
            <label for="name">Challenge Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required>
        </div>

        <div>
            <label for="description">Description</label>
            <textarea id="description" name="description" required>{{ old('description') }}</textarea>
        </div>

        <div>
            <label for="end_date">End Date</label>
            <input id="end_date" type="date" name="end_date" value="{{ old('end_date') }}" required>
        </div>

        <div>
            <label for="max_participants">Max Participants</label>
            <input id="max_participants" type="number" name="max_participants" min="2" max="100" value="{{ old('max_participants') }}" required>
        </div>

        <div>
            <button type="submit" class="create-button">Create</button>
        </div>
    </form>
@endsection
