@extends('layouts.app')

@section('content')
<a href="{{ url()->previous() }}" class="go-back-button">Go Back</a>
<div class="challenge-form">
    <h1>Create Challenge</h1>
    <form method="POST" action="{{ route('challenges.store') }}" enctype="multipart/form-data">
        @csrf

        <div>
            <label for="name">Name</label>
            <input id="name" type="text" name="name" required>
        </div>


        <div>
            <label for="description">Description</label>
            <textarea id="description" name="description" required>{{ old('description') }}</textarea>
        </div>
        <div>
            <label for="end_date">End Date</label>
            <input id="end_date" type="date" name="end_date" required>
        </div>
        <div>
            <div>
                <label for="max_participants">Max Participants</label>
                <input id="max_participants" type="number" name="max_participants" min="1" max="100" required>
            </div>
            <label for="private">Private</label>
            <select id="private" name="private" required>
                <option value="1">Private</option>
                <option value="0">Public</option>
            </select>
        </div>
        <div>
            <button redirect type="submit" class="create-button">Create</button>
        </div>
    </form>
</div>
@endsection