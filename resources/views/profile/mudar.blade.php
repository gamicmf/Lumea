@extends('layouts.app')

@section('content')
<div id="edit-form">
    <h1>Edit Profile</h1>
    <form method="POST" action="{{ route('profile.update', $user->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <fieldset>
            <legend>Personal Information</legend>
            <div>
                <label for="profile_picture">Profile Picture</label>
                <input id="profile_picture" type="file" name="profile_picture">
                @if($user->profile_picture)
                    <img src="{{ asset('storage/profile_pictures/' . $user->profile_picture) }}" alt="Current Profile Picture" style="max-width: 200px; margin-top: 10px;">
                @endif
            </div>
        
                <div>
                    <label for="name">Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" >
                </div>

                <div>
                    <label for="username">Username</label>
                    <input id="username" type="text" name="username" value="{{ old('username', $user->username) }}" >
                </div>
                <div>
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" >
                </div>
                <div>
                    <label for="birthdate">Birthdate</label>
                    <input id="birthdate" type="string" name="birthdate" value="{{ old('birthdate', $user->birthdate) }}" >
                </div> 
        </fieldset>
        <fieldset>
                <legend>Profile Information</legend>   
                <div>
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required>{{ old('description', $user->description) }}</textarea>
                </div>

                <div>

                    <label for="public">Type of Profile</label>
                    <select id="profile" name="public">
                        <option value="1" {{ old('public', $user->public) == 1 ? 'selected' : '' }}>Public</option>
                        <option value="0" {{ old('public', $user->public) == 0 ? 'selected' : '' }}>Private</option>
                    </select>
                </div>
        </fieldset>
        <fieldset>
            <legend>Change Password</legend>
            <button type="button" id="togglePasswordFields" class="btn btn-secondary">Change Password</button>
            <div id="passwordFields" style="display: none;">
                <div>
                    <label for="password">New Password</label>
                    <input id="password" type="password" name="password">
                </div>
                <div>
                    <label for="password_confirmation">Confirm New Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation">
                </div>
            </div>
        </fieldset>
                <div>
                    <button type="submit" class="update-button">Update</button>
                </div>
    </form>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('togglePasswordFields').addEventListener('click', function() {
                var passwordFields = document.getElementById('passwordFields');
                var passwordInput = document.getElementById('password');
                var passwordConfirmationInput = document.getElementById('password_confirmation');

                if (passwordFields.style.display === 'none') {
                    passwordFields.style.display = 'block';
                    passwordInput.setAttribute('required', 'required');
                    passwordConfirmationInput.setAttribute('required', 'required');
                } else {
                    passwordFields.style.display = 'none';
                    passwordInput.removeAttribute('required');
                    passwordConfirmationInput.removeAttribute('required');
                }
            });
        });
    </script>

@endsection

@section('footer')
    @include('layouts.footer')
@endsection

   
