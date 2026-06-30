@extends('layouts.app')

@section('content')

<div class="publication-details">
    <div class="publication-title-and-actions">
        <h1 id="publication-details">Publication Details</h1>
        @if ($publication->post && $publication->post->user && ($publication->post->user->id === Auth::id() || (Auth::check() && Auth::user()->isAdmin())))
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
    </div>
        <div class= "publication-container">
            <img id="publication-image" src="{{ asset('images/' . $publication->pub_image) }}" alt="Publication Image" >
            <div class="publication-info">
                <h2>Posted by: {{ $publication->post->user->name ?? 'Unknown User' }}</h2>
                <p>Username: 
                    <a class="publication-title-username" href="{{ route('profile.showByUsername', $publication->post->user->username) }}">
                        {{ '@' . ($publication->post->user->username ?? 'unknown') }}
                    </a>
                </p>
                <div class="publication-description">
                     <span id="des">Description:</span> 
                     <p>{{ $publication->description }}</p> 
                </div>  
                <p>Posted on: {{ $publication->created_date ?? 'Unknown Date' }}</p>
                @auth
                    <a href="{{ route('report.create', ['type' => 'post','otherId'=>$publication->id]) }}" class="btn btn-danger" style="margin-above=10px">Report</a>
                @else
                    <p><a href="{{ route('login') }}">Log in</a> to report this publication.</p>
                @endauth
                <div class="ranking-section">
                    <p id="ranking">Ranking: {{ number_format($publication->ranking, 1) }} / 5.0</p>

                    <!-- Botão de avaliação -->
                    @auth
                    @php
                    $userVoted = \App\Models\Vote::where('id_publication', $publication->id)
                    ->whereHas('post',function($query){
                        $query->where('id_poster', Auth::id());
                    })->first();     
                    @endphp
                    
                    @if (!$userVoted)

                    <h3>Rate this Publication</h3>
                    <form action="{{ route('publications.rate', $publication->id) }}" method="POST" id="rateForm">
                        @csrf
                        
                        <div class="rating-section">
                            <label>Aesthetic</label>
                            <div class="rating aesthetic-rating">
                                <span class="star" data-value="1" data-field="aesthetic">&#9733;</span>
                                <span class="star" data-value="2" data-field="aesthetic">&#9733;</span>
                                <span class="star" data-value="3" data-field="aesthetic">&#9733;</span>
                                <span class="star" data-value="4" data-field="aesthetic">&#9733;</span>
                                <span class="star" data-value="5" data-field="aesthetic">&#9733;</span>
                            </div>
                            <input type="hidden" name="aesthetic" id="aestheticValue" value="0">
                        </div>
                        
                        <div class="rating-section">
                            <label>Creativity</label>
                            <div class="rating creativity-rating">
                                <span class="star" data-value="1" data-field="creativity">&#9733;</span>
                                <span class="star" data-value="2" data-field="creativity">&#9733;</span>
                                <span class="star" data-value="3" data-field="creativity">&#9733;</span>
                                <span class="star" data-value="4" data-field="creativity">&#9733;</span>
                                <span class="star" data-value="5" data-field="creativity">&#9733;</span>
                            </div>
                            <input type="hidden" name="creativity" id="creativityValue" value="0">
                        </div>
                        
                        <div class="rating-section">
                            <label>Technique</label>
                            <div class="rating technique-rating">
                                <span class="star" data-value="1" data-field="technique">&#9733;</span>
                                <span class="star" data-value="2" data-field="technique">&#9733;</span>
                                <span class="star" data-value="3" data-field="technique">&#9733;</span>
                                <span class="star" data-value="4" data-field="technique">&#9733;</span>
                                <span class="star" data-value="5" data-field="technique">&#9733;</span>
                            </div>
                            <input type="hidden" name="technique" id="techniqueValue" value="0">
                        </div>
                        <button type="submit" id="submit-button" class="btn btn-success mt-3">Submit Rating</button>
                    </form>
                    @else
                    <p class="text-success">You have already rated this publication.</p>
                    @endif
                    @else
                    <p><a href="{{ route('login') }}">Log in</a> to rate this publication.</p>
                    @endauth
                    
                    
                    <style>
                        .rating { font-size: 2rem; color: #ddd; cursor: pointer; }
                        .rating .star:hover, .rating .star.active { color: gold; }
                        .rating-section { margin-bottom: 1rem; }
                    </style>
                </div>

<script>
    const stars = document.querySelectorAll('.star');
    
    stars.forEach(star => {
        star.addEventListener('click', () => {
            const value = star.getAttribute('data-value');
            const field = star.getAttribute('data-field');
            document.getElementById(`${field}Value`).value = value;
            
            // Reset stars for the current field
            const currentSectionStars = document.querySelectorAll(`.${field}-rating .star`);
            currentSectionStars.forEach(s => s.classList.remove('active'));
            
            // Highlight selected stars
            for (let i = 0; i < value; i++) {
                currentSectionStars[i].classList.add('active');
            }
        });
    });
    </script>
        </div>
    </div>



    <div class="comments-container">   
    <h2>Comments</h2>
    
    <d<div class="comments-section">
        @if ($comments->isEmpty())
            <p>No comments yet. Be the first to comment!</p>
        @else
            @foreach ($comments as $comment)
                @if ($comment->previous === null)
                    <div class="comment">
                        @php
                            $post= \App\Models\Post::find($comment->id_post);
                            $user= \App\Models\User::find($post->id_poster);
                        @endphp
                        <small >Posted on {{ $comment->created_date }}</small>
                        <a href="{{ url('/profile/username/' . $user->username) }}" class="publication-link">
                            <p class="user-name"><strong>@ {{ $user->username ?? 'Unknown User' }}</strong>:</p>
                        </a>
                        <p class="comment-text" >{{ $comment->comment_text }}</p>
                        
                        <div class="comment-actions">
                            @if ($comment->post && ($comment->post->id_poster === Auth::id() || (Auth::check() && Auth::user()->isAdmin())))
                                <!-- Botão de Editar -->
                                <button class="edit-button" onclick="showEditForm({{ $comment->id }})">
                                    <i class="fas fa-pencil-alt"></i> 
                                </button>

                                <!-- Formulário de Edição (inicialmente oculto) -->
                                <form id="edit-form-{{ $comment->id }}" action="{{ route('comments.edit', $comment->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    <textarea name="comment_text">{{ $comment->comment_text }}</textarea>
                                    <button type="submit">Salvar</button>
                                </form>

                                <!-- Botão de Excluir -->
                                <form class="dange action="{{ route('comments.destroy', $comment->id) }}" method="POST" style="display: inline;  margin-left:-3px;margin-right: 5px;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> 
                                    </button>
                                </form>
                            @endif
                            <!-- Button to show the response form -->
                            
                            @auth
                                <!-- Response form (initially hidden) -->
                                <button class="button" onclick="showResponseForm({{ $comment->id }})">Reply</button>
                                <div id="response-form-{{ $comment->id }}" class="response-form" style="display: none; margin: 10px;">
                                    <form action="{{ route('comments.reply', $comment->id) }}" method="POST">
                                        @csrf
                                        <textarea name="comment_text" rows="3" placeholder="Write your response..."></textarea>
                                        <button id="submit-reply-button" type="submit">Submit</button>
                                    </form>
                                </div>
                            @else
                                <p><a href="{{ route('login') }}">Log in</a> to reply to this comment.</p>
                            @endauth
                            @auth
                                    <form action="{{ route('comments.like', $comment->id) }}" method="POST">
                                        @csrf
                                        <!-- Coração Branco ou Vermelho, dependendo do estado do Like -->
                                        <button type="submit" class="btn btn-link p-0" style="border: none; background: none;">
                                            <i class="fa fa-heart" 
                                                style="color: {{ $comment->likes()->where('id_user', Auth::id())->exists() ? 'red' : 'white' }}; font-size: 2rem; margin: 10px">
                                            </i>
                                        </button>
                                        <span>{{ $comment->likes()->count() }} likes</span>
                                    </form>
                            @else
                                <form action="{{ route('login')  }}">
                                @csrf
                                    <!-- Coração Branco ou Vermelho, dependendo do estado do Like -->
                                    <button type="submit" class="btn btn-link p-0" style="border: none; background: none;">
                                        <i class="fa fa-heart" 
                                        style="color: {{ $comment->likes()->where('id_user', Auth::id())->exists() ? 'red' : 'white' }}; font-size: 2rem;">
                                        </i>
                                    </button>
                                    <span>{{ $comment->likes()->count() }} likes</span>
                                </form>
                            @endauth
                            
                            <!-- Display replies -->
                            @if ($comment->getReplies($comment->id)->isNotEmpty())
                                <div class="replies">
                                    @foreach ($comment->getReplies($comment->id) as $reply)
                                        @php
                                            $post= \App\Models\Post::find($reply->id_post);
                                            $user= \App\Models\User::find($post->id_poster);
                                        @endphp
                                        <div class="reply">

                                            <p><strong> {{  $user->username ?? 'Unknown User' }}</strong>:</p>

                                            <p>{{ $reply->comment_text }}</p>
                                            @if ($reply->post && ($reply->post->id_poster === Auth::id() || Auth::user()->isAdmin()))
                                            <!-- Botão de Editar -->
                                                <button class="edit-button" onclick="showEditFormReply({{ $reply->id }})">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>

                                                <!-- Formulário de Edição (inicialmente oculto) -->
                                                <form id="edit-form-reply-{{ $reply->id }}" action="{{ route('comments.edit', $reply->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    <textarea name="comment_text">{{ $reply->comment_text }}</textarea>
                                                    <button type="submit">Save</button>
                                                </form>

                                            <!-- Botão de Excluir -->
                                                <form action="{{ route('comments.destroy', $reply->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @auth
                                                <form action="{{ route('comments.like', $reply->id) }}" method="POST">
                                                    @csrf
                                                    <!-- Coração Branco ou Vermelho, dependendo do estado do Like -->
                                                    <button type="submit" class="btn btn-link p-0" style="border: none; background: none;">
                                                        <i class="fa fa-heart" 
                                                        style="color: {{ $reply->likes()->where('id_user', Auth::id())->exists() ? 'red' : 'white' }}; font-size: 2rem;">
                                                        </i>
                                                    </button>
                                                    <span>{{ $reply->likes()->count() }} likes</span>
                                                </form>
                                            @else
                                                <form action="{{ route('login')  }}">
                                                        @csrf
                                                        <!-- Coração Branco ou Vermelho, dependendo do estado do Like -->
                                                        <button type="submit" class="btn btn-link p-0" style="border: none; background: none;">
                                                            <i class="fa fa-heart" 
                                                            style="color: {{ $reply->likes()->where('id_user', Auth::id())->exists() ? 'red' : 'white' }}; font-size: 2rem;">
                                                            </i>
                                                        </button>
                                                        <span>{{ $reply->likes()->count() }} likes</span>
                                                    </form>
                                            @endauth
                                            <small>Posted on {{ $reply->created_date }}</small>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif  
            @endforeach
        @endif
        @auth
            <form action="{{ route('publications.comment', $publication->id) }}" method="POST" class="add-comment-form">
                @csrf
                <textarea name="comment_text" placeholder="Add your comment..." rows="4" maxlength="200" required></textarea>
                <button class="button" type="submit" class="btn btn-primary">Post Comment</button>
            </form>
        @else
            <p><a href="{{ route('login') }}">Log in</a> to add a comment.</p>
        @endauth
   
</div>

    <script>
        function showResponseForm(commentId) {
            var form = document.getElementById('response-form-' + commentId);
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }

        function showEditForm(commentId) {
            // Esconde todos os formulários de edição
            const allEditForms = document.querySelectorAll('[id^="edit-form-"]');
            allEditForms.forEach(form => form.style.display = 'none');

            // Mostra o formulário de edição correspondente ao comentário
            const editForm = document.getElementById(`edit-form-${commentId}`);
            editForm.style.display = 'block';
        }

        function showEditFormReply(replyId) {
        // Esconde todos os formulários de edição de respostas
        const allEditForms = document.querySelectorAll('[id^="edit-form-reply-"]');
        allEditForms.forEach(form => form.style.display = 'none');

        // Mostra o formulário de edição correspondente à resposta
        const editForm = document.getElementById(`edit-form-reply-${replyId}`);
        editForm.style.display = 'block';
        }
        
    </script>

@endsection

@section('footer')
    @include('layouts.footer')
@endsection
