@extends('layouts.app')

@section('content')
<div class="container about-container">
    <h1>About Us</h1>
    <p>Welcome to Lumea! The main goal of this new social network is not only to allow photographer to showcase their work but also to help them improve by participating in different photography challenges. The winner of this challenges is decided by the Lumea community.</p>
    
    <h2>Main Features</h2>
    <div class="feature-cards">
        <div class="feature-card">
            <h3>For Visitors</h3>
            <ul>
                <li>Login/Logout</li>
                <li>Recover Password</li>
                <li>View Challenges</li>
                <li>View Public Users/Groups</li>
                <li>View Public Timeline</li>
            </ul>
        </div>    

        <div class="feature-card">
            <h3>For Authenticated Users</h3>
            <ul>
                <li>Manage Follows/Follow Requests</li>
                <li>Group Interactions</li>
                <li>Challenge Interactions</li>
                <li>Create and Manage Posts</li>
                <li>Write and React Post Comments</li>
                <li>Profile Management</li>
                <li>Notifications of All Type</li>
                <li>Search Multiple Ways</li>
            </ul>
        </div> 
        <div class="feature-card">
            <h3>For Administrators</h3>
            <ul>
                <li>Administer User Accounts</li>
                <li>Administer Groups, Challenges and Posts</li>
                <li>Block and Unblock User Accounts</li>
                <li>Create Public Challenges</li>
            </ul>
        </div> 
        <div class="feature-card">
            <h3>Features Included to Help You:</h3>
            <ul>
                <li>Placeholders in form inputs</li>
                <li>Contextual error messages</li>
                <li>Contextual help</li>
                <li>FAQ</li>
                <li>About Us/Contacts/Main Features</li>
            </ul>
        </div> 
    </div>    
<div class="authors-section">
    <h2>Contacts</h2>
    <p>This website was created by a team of dedicated developers. Any question, ask them:</p>
    <ul>
        @foreach ($admins as $admin)
            <li><a href="{{ route('profile.showByUsername', $admin->username) }}" class="help-admin-info">{{$admin->name}}</a>: {{$admin->email}}</li>
        @endforeach
    </ul>    
</div>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection