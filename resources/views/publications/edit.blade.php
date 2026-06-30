@extends('layouts.app')

@section('content')
<form id="edit-form" method="POST" action="{{ route('publications.update', $publication->id) }}" enctype="multipart/form-data">
        <h1>Edit Publication</h1>
        @csrf
        @method('PUT')

        <div>
            <label for="pub_image">Image</label>
            <input id="pub_image" type="file" name="pub_image" onchange="previewImage(event)">
            @if ($publication->pub_image)
                <img id="current-image" src="{{ asset('images/' . $publication->pub_image) }}" alt="Current Image" style="max-width: 200px; margin-top: 10px;">
            @endif
            <img id="preview-image" src="#" alt="New Image Preview" style="display: none; max-width: 200px; margin-top: 10px;">
        </div>

        <div>
            <label for="description">Description</label>
            <textarea id="description" name="description" required>{{ old('description', $publication->description) }}</textarea>
        </div>

        <div>
            <button type="submit" class="update-button">Update</button>
        </div>
    </form>
@endsection

@section('scripts')
<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('preview-image');
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);

        // Ocultar a imagem atual
        var currentImage = document.getElementById('current-image');
        if (currentImage) {
            currentImage.style.display = 'none';
        }
    }
</script>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection