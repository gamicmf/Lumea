<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;

class PostPolicy
{
    /**
     * Determine se o usuário pode relatar um post.
     *
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function report(User $user, Post $post)
    {
        // Permitir que qualquer usuário autenticado relate um post, exceto o próprio autor do post
        return $user->id !== $post->user_id;
    }
}
