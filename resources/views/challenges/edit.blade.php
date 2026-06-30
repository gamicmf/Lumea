@extends('layouts.app')

@section('content')
<a href="{{ route('challenges.show', $challenge->id) }}" class="go-back-button">Go Back</a>
<div class="container">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <div class="header-challenge-container">
        <h1 class="challenge-title-edit">Edit Challenge Information</h1>
        <form action="{{ route('challenges.destroy', $challenge->id) }}" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="delete-challenge-button" onclick="return confirm('Are you sure you want to delete this challenge?')">
                <i class="fas fa-trash"></i>
                Delete Challenge
            </button>
        </form>
    </div>
    <div class="spacer"></div>
    <div id="edit-info-section" class="section">
        <form action="{{ route('challenges.update', $challenge->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="edit-challenge-field">
                <label for="name" class="challenge-info-edit">Challenge Name:</label>
                <input type="text" name="name" id="name" class="challenge-edit-box" value="{{ $challenge->name }}">
            </div>
            <div class="edit-challenge-field">
                <label for="description" class="challenge-info-edit">Challenge Description:</label>
                <textarea name="description" id="description" class="challenge-edit-box">{{ $challenge->description }}</textarea>
            </div>
            <div class="edit-challenge-field">
                <label for="end_date" class="challenge-info-edit">End Date:</label>
                <input type="date" name="end_date" id="end_date" class="challenge-edit-box" value="{{ $challenge->end_date }}">
            </div>
            <div class="edit-challenge-field">
                <label for="max_participants" class="challenge-info-edit">Max Participants:</label>
                <input type="number" name="max_participants" id="max_participants" class="challenge-edit-box" value="{{ $challenge->max_participants }}" min="1" max="100">
            </div>
            <div class="edit-challenge-field">
                <label for="private" class="challenge-info-edit">Private:</label>
                <select name="private" id="private" class="challenge-edit-box">
                    <option value="1" {{ $challenge->private ? 'selected' : '' }}>Private</option>
                    <option value="0" {{ !$challenge->private ? 'selected' : '' }}>Public</option>
                </select>
            </div>
            <button type="submit" class="save-changes-button">Save Changes</button>
        </form>
    </div>
</div>
@endsection
