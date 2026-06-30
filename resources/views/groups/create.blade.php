@extends('layouts.app')

@section('content')
    <h1>Create Group</h1>
    <form method="POST" action="{{ route('groups.store') }}" enctype="multipart/form-data">
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
            <label for="public">Public</label>
            <select id="public" name="public" required>
                <option value="1">Public</option>
                <option value="0">Private</option>
            </select>
        </div>

        <div>
            <label for="max_participants">Max Participants</label>
            <input id="max_participants" type="number" name="max_participants" min="1" max="100" required>
        </div>

        <div>
            <label for="group_image">Group Image</label>
            <input id="group_image" type="file" name="group_image">
        </div>

        <div>
            <button type="submit" class="create-button">Create</button>
        </div>
    </form>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection