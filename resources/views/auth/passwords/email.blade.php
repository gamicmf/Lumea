@extends('layouts.app')

@section('content')
<a href="{{ route('login') }}" class="go-back-button">Go Back</a>
    <div class="container">
        <h2>Reset Password</h2>
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label for="email-adress">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <button type="submit" class="botao">Send Password Reset Link</button>
        </form>
    </div>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection