<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StaticPagesPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view the FAQ page.
     *
     * @param  \App\Models\User|null  $user
     * @return bool
     */
    public function viewFaq(?User $user)
    {
        // Qualquer usuário, autenticado ou não, pode visualizar a página de FAQ
        return true;
    }

    /**
     * Determine if the user can view the About page.
     *
     * @param  \App\Models\User|null  $user
     * @return bool
     */
    public function viewAbout(?User $user)
    {
        // Qualquer usuário, autenticado ou não, pode visualizar a página About
        return true;
    }

    /**
     * Determine if the user can send a question to the admins.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function sendQuestion(User $user)
    {
        // Apenas usuários autenticados podem enviar perguntas
        return $user->exists();
    }
}
