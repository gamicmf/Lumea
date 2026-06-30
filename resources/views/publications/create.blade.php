@extends('layouts.app')

@section('content')
    <h1>Create Publication</h1>
    <form method="POST" action="{{ route('publications.store',['challenge_id' => $challenge_id])}}" enctype="multipart/form-data">
        @csrf

        <div>
            <label for="pub_image">Image</label>
            <input id="pub_image" type="file" name="pub_image" required>
        </div>

        <div>
            <label for="description">Description</label>
            <textarea id="description" name="description" required>{{ old('description') }}</textarea>
        </div>

        <div>
            <button type="submit" class="create-button">Create</button>
        </div>
    </form>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection