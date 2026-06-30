<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use App\Models\Post;
use App\Models\Publication;
use App\Models\User;
use App\Models\Comment;
use App\Models\Report;
use App\Models\Notification;
use App\Models\Challenge;
use App\Models\Notifications\CommentNotification;
use App\Events\NotificationPusher;



class PublicationController extends Controller
{
    /**
     * Show the publication for a given id.
     */
    public function show($id)
    {
        $publication = \App\Models\Publication::findOrFail($id);
    
        // Buscar comentários associados à publicação, ordenados pela data
        $comments = \App\Models\Comment::with('post')->where('id_publication', $id)
            ->orderBy('created_date', 'asc')
            ->get();
    
        return view('publications.show', compact('publication', 'comments'));
    }
    
    public function index(Request $request)
    {
        // Recuperar todas as publicações
        $publicationsQuery = Publication::with('post.user');

        if ($request->has('filter')) {
            $filter = $request->input('filter');

            switch ($filter) {
                case 'latest':
                    $publicationsQuery->orderBy('created_date', 'desc');
                    break;

                case 'follow':
                    if (Auth::check()) {
                        $userId = Auth::id();
                        $publicationsQuery->whereHas('post.user', function ($query) use ($userId) {
                            $query->whereIn('id', function ($subQuery) use ($userId) {
                                $subQuery->select('id_followed')
                                        ->from('follow_request')
                                        ->where('id_follower', $userId);
                            });
                        });
                    }
                    break;

                case 'best':
                    $publicationsQuery->orderBy('ranking', 'desc');
                    break;

                case 'all':
                default:
                    // Nenhum filtro adicional necessário
                    break;
            }
        }

        // Se o usuário estiver logado, vamos ocultar as publicações de perfis privados
        if (Auth::check() && !Auth::user()->isAdmin()) {
            // Filtra publicações de usuários com perfil público
            $publicationsQuery->whereHas('post.user', function($query) {
                $query->where('public', true); // Exibe apenas as publicações de usuários com perfil público
            });
        }

        // Se for uma pesquisa por usuário, filtra pelas publicações do usuário específico
        if ($request->has('username')) {
            $username = $request->get('username');
            $publicationsQuery->whereHas('post.user', function($query) use ($username) {
                $query->where('username', 'like', '%' . $username . '%');
            });
        }

        $publications = $publicationsQuery->get();

        return view('publications.index', compact('publications'));
    }


    /**
     * Shows the user's publications.
     */
    public function list()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $publications = Auth::user()->publications()->with('post.user')->orderBy('created_date', 'desc')->get();
        $this->authorize('list', Publication::class);

        return view('publications.index', [
            'publications' => $publications
        ]);
    }

    /**
     * Show the form for creating a new publication.
     */
    public function create($challenge_id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $challenge = Challenge::findOrFail($challenge_id);

        $user = Auth::user();
        $isParticipating = DB::table('publications')
        ->join('post', 'publications.id_post', '=', 'post.id') 
        ->where('publications.id_challenge', $challenge_id)
        ->where('post.id_poster', $user->id)
        ->exists();
    
        if ($isParticipating) {
            return redirect()
                ->back()
                ->with('error', 'You have already submitted a publication for this challenge.');
        }
        return view('publications.create', ['challenge_id' => $challenge_id]);
    }

    /**
     * Store a newly created publication in storage.
     */
    public function store(Request $request, $challenge_id)
    {
        $this->authorize('create', Publication::class);

        $request->validate([
            'pub_image' => 'required|image|mimes:png,jpg,jpeg,heic|max:10048',
            'description' => 'required|string|max:200',
        ]);

        $challenge = Challenge::findOrFail($challenge_id);

        $post = Post::create([
            'id_poster' => Auth::id(),
            'edited' => false,
        ]);

        $publication = new Publication();
        $publication->id_post = $post->id;
        $publication->id_challenge = $challenge->id; // Associa publicação a um desafio
        $path = $request->file('pub_image')->move(public_path('images'), $request->file('pub_image')->getClientOriginalName());
        $publication->pub_image = $request->file('pub_image')->getClientOriginalName();
        $publication->ranking = 0; // Valor padrão para ranking
        $publication->description = $request->input('description');
        $publication->created_date = now(); // Define a data de criação
        $publication->save();

        $participate=DB::table('challenge_participants')->where('id_challenge', $challenge_id)->where('id_user', Auth::id())->exists();
        
        if(!$participate){
            DB::table('challenge_participants')->insert([
                'id_challenge' => $challenge_id,
                'id_user' => Auth::id()
            ]);
        }
        return redirect()->route('challenges.show', $challenge_id)->with('success', 'Publication created successfully!');
    }

    /**
     * Deletes a publication.
     */
    public function delete(Request $request, $id)
    {
        $publication = Publication::find($id);

        if (!$publication) {
            return redirect()->route('publications.index')->with('error', 'Publication not found');
        }

        // Verificação de autorização
        $this->authorize('delete', $publication);

        
        // Apaga a imagem associada, se houver
        if ($publication->pub_image) {
            Storage::delete('public/images/' . $publication->pub_image);
        }

        $challenge=Challenge::findOrFail($publication->id_challenge);
        $participate=DB::table('challenge_participants')->where('id_challenge', $challenge->id)->where('id_user', Auth::id())->exists();
        if($participate){
            DB::table('challenge_participants')->where('id_challenge', $challenge->id)->where('id_user', Auth::id())->delete();
        }
        $publication->delete();

        return redirect()->route('publications.index')->with('success', 'Publication deleted successfully');
    }

    public function edit($id)
    {
        $publication = Publication::findOrFail($id);
        //Route::post('publications/{id}/comment', [PublicationController::class, 'addComment'])->name('publications.comment');

        // Verificação de autorização
        $this->authorize('update', $publication);

        return view('publications.edit', compact('publication'));
    }


    /**
     * Updates a publication's image and description.
     */
    public function update(Request $request, $id)
    {
        $publication = Publication::findOrFail($id);

        // Verificação de autorização
        $this->authorize('update', $publication);

        // Validação dos dados do formulário
        $request->validate([
            'description' => 'nullable|string|max:200',
            'pub_image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        // Atualiza a descrição, se fornecida
        if ($request->has('description')) {
            $publication->description = $request->input('description');
        }

        // Atualiza a imagem, se uma nova for fornecida
        if ($request->hasFile('pub_image')) {
            // Apaga a imagem antiga, se existir
            if ($publication->pub_image && file_exists(public_path('images/' . $publication->pub_image))) {
                unlink(public_path('images/' . $publication->pub_image));
            }
        
            // Move a nova imagem para public/images
            $path = $request->file('pub_image')->move(public_path('images'), $request->file('pub_image')->getClientOriginalName());
        
            // Atualiza o nome do arquivo
            $publication->pub_image = $request->file('pub_image')->getClientOriginalName();
        }

        // Salva as alterações no banco de dados
        $publication->save();

        return redirect()->route('publications.index')->with('success', 'Publication updated successfully!');
    }


    public function reportPublication($request, $id){
        $request->validate([
            'reportable_id' => 'required|integer',
            'reportable_type' => 'required|string',
            'description' => 'required|string|max:255',
        ]);
        $report = new Report();
        $report->id_user = Auth::id();
        $report->reportable_id = $id;
        $report->reportable_type = 'post';
        $report->description = $request->input('description');
        $report->created_at = now();
        $report->updated_at = now();

        $report->save();

        return redirect()->back()->with('success', 'Publication reported successfully!');
    }
    
    public function post()
{
    return $this->belongsTo(Post::class, 'id_post');
}


    public function commentPublication(Request $request, $id)
    {
        $request->validate([
            'comment_text' => 'required|string|max:200',
        ]);
    
        $publication = \App\Models\Publication::findOrFail($id);
    
        $post_publication= \App\Models\Post::findOrFail($publication->id_post);

        $post_comment=new \App\Models\Post();
        $post_comment->id_poster=Auth::user()->id;
        $post_comment->save();

        $comment= \App\Models\Comment::create([
            'id_post' => $post_comment->id,
            'id_publication' => $publication->id,
            'comment_text' => $request->input('comment_text'),
            'created_date' => now(),
        ]);
        
        if($post_publication->id_poster!=Auth::user()->id){
            $notification= new \App\Models\Notification();
            $notification->received_user = $post_publication->id_poster;
            $notification->emitter_user = Auth::user()->id;
            $notification->date= now();
            $notification->save();


            $commentNotification= new \App\Models\Notifications\CommentNotification();
            $commentNotification->id = $notification->id;
            $commentNotification->id_comment = $comment->id;
            $commentNotification->notification_type = 'comment_publication';
            $commentNotification->save();

            event(new NotificationPusher($notification->id, $notification->received_user));

        }

        return redirect()->route('publications.show', $id)->with('success', 'Comment added successfully!');
    }

    public function replyComment(Request $request, $id)
    {
        $request->validate([
            'comment_text' => 'required|string|max:200',
        ]);
    
        $ParentComment = \App\Models\Comment::findOrFail($id);
        $post_parentcomment= \App\Models\Post::findOrFail($ParentComment->id_post);
        $publication = \App\Models\Publication::findOrFail($ParentComment->id_publication);

        $post_publication= \App\Models\Post::findOrFail($publication->id_post);

        $post_comment=new \App\Models\Post();
        $post_comment->id_poster=Auth::user()->id;
        $post_comment->save();

        $comment= \App\Models\Comment::create([
            'id_post' => $post_comment->id,
            'id_publication' => $publication->id,
            'previous' => $ParentComment->id,
            'comment_text' => $request->input('comment_text'),
            'created_date' => now(),
        ]);
        
        if($post_publication->id_poster!=Auth::user()->id){
            $notification_o_pub= new \App\Models\Notification();
            $notification_o_pub->received_user = $post_publication->id_poster;
            $notification_o_pub->emitter_user = Auth::user()->id;
            $notification_o_pub->date= now();
            $notification_o_pub->save();

            $commentNotification_o= new \App\Models\Notifications\CommentNotification();
            $commentNotification_o->id = $notification_o_pub->id;
            $commentNotification_o->id_comment = $comment->id;
            $commentNotification_o->notification_type = 'comment_publication';
            $commentNotification_o->save();

            event(new NotificationPusher($notification_o_pub->id, $notification_o_pub->received_user));

        }
        
        //não precisa de notificação para o owner do parent comment
        //ele está se a responder a ele mesmo
        if($post_parentcomment->id_poster!=Auth::user()->id){

    
            $post_parentcomment= \App\Models\Post::findOrFail($ParentComment->id_post);
            $not_o_com_replied= new \App\Models\Notification();
            $not_o_com_replied->received_user = $post_parentcomment->id_poster;
            $not_o_com_replied->emitter_user = Auth::user()->id;
            $not_o_com_replied->date= now();
            $not_o_com_replied->save();
        

            $commentNotification= new \App\Models\Notifications\CommentNotification();
            $commentNotification->id = $not_o_com_replied->id;
            $commentNotification->id_comment = $comment->id;
            $commentNotification->notification_type = 'reply_comment';
            $commentNotification->save();

            event(new NotificationPusher($not_o_com_replied->id, $not_o_com_replied->received_user));

        }
        //notification para o owner da publication
        
        return redirect()->route('publications.show', $publication->id)->with('success', 'Comment added successfully!');
    }
    
    
    public function respond_Comment(Request $request, $id, $id_comment)
    {
        $request->validate([
            'comment_text' => 'required|string|max:200',
        ]);
    
        $publication = \App\Models\Publication::findOrFail($id);
        $post= \App\Models\Post::findOrFail($publication->id_post);

        $comment = new Comment();
        $comment->id_post = $post->id;
        $comment->id_publication = $id;
        $comment->comment_text = $request->input('comment_text');
        $comment->created_date = now();
        $comment->save();


        $notification_pub_owner = new Notification();
        $notification_pub_owner->received_user = $post->id_poster;
        $notification_pub_owner->emitter_user = Auth::id();
        $notification_pub_owner->viewed = false;
        $notification_pub_owner->date = now();
        $notification_pub_owner->save();

        $comment_Notification= new CommentNotification();
        $comment_Notification->id = $notification_pub_owner->id;
        $comment_Notification->id_comment = $comment->id;
        $comment_Notification->notification_type='comment_publication';
        $comment_Notification->save();

        event(new NotificationPusher($notification_pub_owner->id, $notification_pub_owner->received_user));

        return redirect()->route('publications.show', $id)->with('success', 'Comment added successfully!');
    }



    public function rate(Request $request, $id)
    {
        $request->validate([
            'aesthetic' => 'required|numeric|min:1|max:5',
            'creativity' => 'required|numeric|min:1|max:5',
            'technique' => 'required|numeric|min:1|max:5',
        ]);
    
        $publication = \App\Models\Publication::findOrFail($id);
    
        // Verificar se o usuário já votou
        $already_voted = \App\Models\Vote::where('id_publication', $id)
                                         ->whereHas('post', function ($query) {
                                             $query->where('id_poster', Auth::user()->id);
                                         })->first();
    
        if ($already_voted) {
            return redirect()->route('publications.show', $id)->with('error', 'You already voted this publication!');
        }
    
        // Criar novo post de voto
        $post_vote = new \App\Models\Post();
        $post_vote->id_poster = Auth::user()->id;
        $post_vote->save();
    
        // Salvar o voto
        $vote = \App\Models\Vote::create([
            'id_post' => $post_vote->id,
            'id_publication' => $publication->id,
            'aesthetic' => $request->input('aesthetic'),
            'creativity' => $request->input('creativity'),
            'technique' => $request->input('technique'),
            'rate' => ($request->input('aesthetic') + $request->input('creativity') + $request->input('technique')) / 3,
            'created_date' => now(),
        ]);
    
        // Recalcular a média de votos para a publicação
        $averageRating = \App\Models\Vote::where('id_publication', $publication->id)
                                         ->avg('rate');
    
        // Atualizar o campo ranking da publicação
        $publication->ranking = $averageRating;
        $publication->save();
    
        // Notificar o dono da publicação se o avaliador for diferente
        $post_publication = \App\Models\Post::findOrFail($publication->id_post);
        if ($post_publication->id_poster != Auth::user()->id) {
            $notification = new \App\Models\Notification();
            $notification->received_user = $post_publication->id_poster;
            $notification->emitter_user = Auth::user()->id;
            $notification->date = now();
            $notification->save();
    
            $voteNot = new \App\Models\Notifications\PublicationNotification();
            $voteNot->id = $notification->id;
            $voteNot->id_publication = $publication->id;
            $voteNot->notification_type = 'vote_post';
            $voteNot->save();
            
            event(new NotificationPusher($notification->id, $notification->received_user));


        }
    
        return redirect()->back()->with('success', 'Thank you for your ratings!');
    }
    
}