
@extends('layouts.app')

@section('content')
<form class="login-form" method="POST" action="{{ route('register') }}">
    @csrf

    <div>
        <label for="name">Name <span class="required-asterisk">*</span></label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
        @error('name')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="username">Username <span class="required-asterisk">*</span></label>
        <input id="username" type="text" name="username" value="{{ old('username') }}" required>
        @error('username')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="email">Email <span class="required-asterisk">*</span></label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required>
        @error('email')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="password">Password <span class="required-asterisk">*</span></label>
        <input id="password" type="password" name="password" required>
        @error('password')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="password-confirmation">Confirm Password <span class="required-asterisk">*</span></label>
        <input id="password-confirm" type="password" name="password_confirmation" required>
    </div>

    <div>
        <label for="birthdate">Birthdate <span class="required-asterisk">*</span></label>
        <input id="birthdate" type="date" name="birthdate" value="{{ old('birthdate') }}" required>
    </div>
    <p>*are mandatory</p>
    <button type="submit" class="register-button">
        Register
    </button>

    <a href="{{ route('google-auth') }}" class="google-button">
        <img src="{{ asset('images/google-icon.png') }}" alt="GoogleIcon" class="google-icon">
        Register with Google
    </a>

</form>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection