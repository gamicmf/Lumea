@extends('layouts.app')

@section('content')
<a href="{{ route('password.request') }}" class="go-back-button">Go Back</a>
    <div class="container">
        <h2>Enter Token</h2>
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <form method="POST" action="{{ route('password.verifyToken') }}">
            @csrf
            <div class="form-group">
                <label for="token">Token</label>
                <input type="text" name="token" id="token" class="form-control" required>
            </div>
            <button type="submit" class="verify-button">Verify Token</button>
        </form>
    </div>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection