@extends('layouts.app')

@section('content')
    <a href="{{ route('admin.panel') }}" class="go-back-button">Go Back</a>
    <div class="container">
        <h1>Answer FAQ</h1>
        <form action="{{ route('admin.answerFaq', $faq->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="question">Question</label>
                <input type="text" id="question" class="form-control" value="{{ $faq->question }}" disabled>
            </div>
            <div class="form-group">
                <label for="answer">Answer</label>
                <textarea name="answer" id="answer" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection