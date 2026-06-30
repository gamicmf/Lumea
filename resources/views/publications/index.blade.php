@extends('layouts.app')
@section('content')
    <div class="container" id="publications-index-container">
    <div class="header-publication-container">
        <h1>Publications</h1>
        <div class="header-container">
            <div class="filter-container">
                <span id="filter-toggle" class="filter-toggle">
                    <i class="fas fa-filter"></i> Filter By
                </span>
                <div id="filter-options" class="filter-options" style="display: none;">
                    <div class="filter-option" data-value="latest">Latest Publications</div>
                    <div class="filter-option" data-value="follow">That I Follow</div>
                    <div class="filter-option" data-value="best">Best Rating</div>
                    <div class="filter-option" data-value="all">All</div>
                </div>
            </div>
        </div>
    </div>
    <div class="publications-container">
        @foreach ($publications as $publication)
                <div class="publication">
                    @if ($publication->post && $publication->post->user && ($publication->post->user->id === Auth::id() ||(Auth::check() && Auth::user()->isAdmin())))
                        <div class="publication-actions">
                            <a href="{{ route('publications.edit', $publication->id) }}" class="edit-button">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <form action="{{ route('publications.destroy', $publication->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-button">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    @endif
                        <a href="{{ url('publications/show/' . $publication->id) }}" class="publication-link">
                            <img src="{{ asset('images/' . $publication->pub_image) }}" alt="Publication Image">
                            <div class="publication-content">
                                @if ($publication->post && $publication->post->user)
                                <p class="publication-description">{{ $publication->description }}</p>
                                    <h2 class="publication-title">{{ 'By: '. $publication->post->user->name }}</h2>
                                    <h2 class="publication-title-username">
                                        <a href="{{ route('profile.showByUsername', $publication->post->user->username) }}">
                                            {{ '@' . $publication->post->user->username }}
                                        </a>
                                    </h2>
                                @else
                                    <h2 class="publication-title">Unknown User</h2>
                                    @endif
                                <div class="publication-meta">
                                    <span>Ranking: <span id="ranking">{{ number_format($publication->ranking, 1) }} / 5.0</span></span>
                                    @if ($publication->post)
                                        <span>Posted on: {{ $publication->created_date }}</span>
                                    @else
                                        <span>Posted on: Unknown Date</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
        @endforeach
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this publication?
                </div>
                <div class="modal-footer">
                    <form id="delete-form" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-danger">Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var publicationId = button.data('id');
        var action = '{{ route("publications.destroy", ":id") }}';
        action = action.replace(':id', publicationId);
        $('#delete-form').attr('action', action);
    });
</script>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@section('scripts')
    <script src="{{ asset('js/publication_filter.js') }}"></script>
@endsection