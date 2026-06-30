@extends('layouts.app')

@section('content')

    <div class="container">
        <h1>Report</h1>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <form action="{{ route('report.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="hidden" name="otherId" value="{{ $otherId }}">
            <div class="form-group">
                <label for="report">Report</label>
                <textarea name="report" id="report" class="form-control" rows="4" required minlength="10" maxlength="255"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

@endsection

@section('footer')
    @include('layouts.footer')
@endsection