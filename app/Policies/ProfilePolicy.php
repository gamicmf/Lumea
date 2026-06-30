<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfilePolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário pode editar o perfil.
     */
    public function edit(User $authUser, User $profileUser)
    {
        // Apenas o próprio usuário ou um administrador pode editar o perfil
        return $authUser->id === $profileUser->id || $authUser->isAdmin();
    }

    /**
     * Determina se o usuário pode visualizar o perfil.
     */
    public function view(User $authUser = null, User $profileUser)
    {
        // Perfis públicos são sempre visíveis
        if ($profileUser->public) {
            return true;
        }

        // Perfis privados só podem ser vistos pelo próprio usuário, administradores ou seguidores
        return $authUser && ($authUser->id === $profileUser->id || $authUser->isAdmin() || $authUser->isFollowing($profileUser));
    }

    /**
     * Determina se o usuário pode seguir outro usuário.
     */
    public function follow(User $authUser, User $profileUser)
    {
        // Um usuário não pode seguir a si mesmo
        return $authUser->id !== $profileUser->id;
    }

    /**
     * Determina se o usuário pode visualizar os seguidores ou as pessoas que está seguindo.
     */
    public function viewFollowers(User $authUser, User $profileUser)
    {
        // Permite visualizar seguidores se o perfil for público ou se for o próprio usuário ou um administrador
        return $profileUser->public || $authUser->id === $profileUser->id || $authUser->isAdmin();
    }

    /**
     * Determina se o usuário pode aceitar ou rejeitar uma solicitação de seguir.
     */
    public function manageFollowRequest(User $authUser, User $profileUser)
    {
        // Apenas o próprio usuário ou um administrador pode gerenciar solicitações de seguir
        return $authUser->id === $profileUser->id || $authUser->isAdmin();
    }
}
