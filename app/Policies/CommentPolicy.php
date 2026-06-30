<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Verifica se o usuário pode editar o comentário.
     */
    public function edit(User $user, Comment $comment)
    {
        // Permite a edição se o usuário for o autor do post relacionado ou for admin
        return $comment->post && $comment->post->id_poster === $user->id || $user->isAdmin();
    }

    /**
     * Verifica se o usuário pode excluir o comentário.
     */
    public function delete(User $user, Comment $comment)
    {
        // Permite a exclusão se o usuário for o autor do post relacionado ou for admin
        return $comment->post && $comment->post->id_poster === $user->id || $user->isAdmin();
    }

    /**
     * Verifica se o usuário pode curtir o comentário.
     */
    public function like(User $user, Comment $comment)
    {
        // Permite o like se o usuário estiver autenticado
        return $user !== null;
    }
}
