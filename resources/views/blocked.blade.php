@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Your account is blocked</h1>
        <p>Please contact support for more information.</p>
        <div class="header-container">
            <a class="button new-button edit-button-profile"   href="{{ route('about') }}">Contacts</a>
        </div>
       
    </div>
@endsection