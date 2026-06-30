<?php
namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LikeComment;
use App\Models\Post;
use App\Models\Notification;
use App\Models\Notifications\CommentNotification;
use App\Events\NotificationPusher;


class CommentController extends Controller
{
    // Editar comentário
    public function edit(Request $request, $commentId)
    {
        // Encontrar o comentário
        $comment = Comment::with('post')->findOrFail($commentId);

        $idPoster = $comment->post ? $comment->post->id_poster : null;

        // Verificar se o usuário é o autor ou admin
        if (  $idPoster === Auth::id() || Auth::user()->isAdmin()) {
            $comment->comment_text = $request->input('comment_text');
            $comment->save();
        }

        // Atualizar comentário


        return redirect()->route('publications.show', $comment->id_publication)
            ->with('success', 'Comentário atualizado com sucesso.');
    }

    // Excluir comentário
    public function destroy($commentId)
    {
        // Encontrar o comentário
        $comment = Comment::with('post')->findOrFail($commentId);

        $idPoster = $comment->post ? $comment->post->id_poster : null;
    
        // Verificar se o usuário tem permissão para excluir
        if  ( $idPoster === Auth::id() || Auth::user()->isAdmin()){
            
    
            // Excluir respostas relacionadas ao comentário
            Comment::where('previous', $commentId)->delete();
    
            // Excluir o comentário principal
            $comment->delete();
    
            return redirect()->route('publications.show', $comment->id_publication)
                ->with('success', 'Comentário excluído com sucesso!');
        }
    
        return redirect()->route('publications.show', $comment->id_publication)
            ->with('error', 'Você não tem permissão para excluir este comentário.');
    }
    
    public function like($commentId)
    {
        $user = Auth::user();  // Verifica o usuário autenticado
        $comment = Comment::find($commentId);  // Encontra o comentário
        $post_comment=Post::find($comment->id_post);

        if (!$comment) {
            return redirect()->back()->with('error', 'Comment not found');
        }

        // Verifica se o usuário já deu like
        $existingLike = LikeComment::where('id_user', $user->id)
            ->where('id_comment', $commentId)
            ->first();

        if ($existingLike) {
            // Se já deu like, remova o like (unlike)
            $existingLike->delete();
            return redirect()->back();  // Redireciona para a página com a atualização do número de likes
        } else {
            // Caso contrário, adicione o like
            $post=new Post();
            $post->id_poster=Auth::id();
            $post->save();
            
            LikeComment::create([
                'id_user' => $user->id,
                'id_comment' => $comment->id,
                'id_post' => $post->id
            ]);

            if($post_comment->id_poster!=Auth::id()){
                $notification= new Notification();
                $notification->emitter_user=Auth::id();
                $notification->received_user=$post_comment->id_poster;
                $notification->date=now();
                $notification->save();
                $commentNotification= new CommentNotification();
                $commentNotification->id=$notification->id;
                $commentNotification->id_comment=$comment->id;
                $commentNotification->notification_type= 'liked_comment';
                $commentNotification->save();

                event(new NotificationPusher($notification->id, $notification->received_user));
            }

            return redirect()->back();  // Redireciona para a página com a atualização do número de likes
        }
    }
    
    
}
