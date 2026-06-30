@extends('layouts.app')

@section('content')
    <form class = "login-form" method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}

        <label for="email">E-mail or username</label>
        <input id="email" type="text" name="email" value="{{ old('email') }}" required autofocus>
        @if ($errors->has('email'))
            <span class="error">
            {{ $errors->first('email') }}
            </span>
        @endif

        <label for="password" >Password</label>
        <input id="password" type="password" name="password" required>
        @if ($errors->has('password'))
            <span class="error">
                {{ $errors->first('password') }}
            </span>
        @endif

        <label for="remember">
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
        </label>

        <button class="login-button" type="submit">
            Login
        </button>
        @if (session('success'))
            <p class="success">
                {{ session('success') }}
            </p>
        @endif

        <a href="{{ route('google-auth') }}" class="google-button">
            <img src="{{ asset('images/google-icon.png') }}" alt="Google Icon" class="google-icon">
            Continue with Google
        </a>

        @if (Route::has('password.request'))
            <a  class="btn btn-link" href="{{ route('password.request') }}">
                Forgot your Password?
            </a>
        @endif
    </form>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection