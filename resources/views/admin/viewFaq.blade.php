@extends('layouts.app')

@section('content')
    <a href="{{ route('admin.panel') }}" class="go-back-button">Go Back</a>
    <div class="container">
        <h1>FAQ Answer</h1>
        <form id="editAnswerForm" action="{{ route('admin.answerFaq', $faq->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="question">Question</label>
                <input type="text" id="question" class="form-control" value="{{ $faq->question }}" disabled>
            </div>
            <div class="form-group">
                <label for="answer">Answer</label>
                <div class="input-group">
                    <textarea id="answer" name="answer" class="form-control" rows="4" disabled>{{ $faq->answer }}</textarea>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="editButton">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-2" id="saveButton" style="display: none;">Save</button>
        </form>
    </div>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection