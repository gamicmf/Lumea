<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Challenge;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChallengesPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any challenges.
     *
     * @param  \App\Models\User|null  $user
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        return true; // Qualquer usuário pode visualizar desafios públicos
    }

    /**
     * Determine if the user can view a specific challenge.
     *
     * @param  \App\Models\User|null  $user
     * @param  \App\Models\Challenge  $challenge
     * @return bool
     */
    public function view(?User $user, Challenge $challenge): bool
    {
        if ($challenge->private) {
            return $user && ($user->isAdmin() || $challenge->id_creator == $user->id || $challenge->participants->contains($user->id));
        }
        return true; // Desafios públicos podem ser visualizados por qualquer pessoa
    }

    /**
     * Determine if the user can create a challenge.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user && $user->isAdmin(); // Apenas administradores podem criar desafios
    }

    /**
     * Determine if the user can update a challenge.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Challenge  $challenge
     * @return bool
     */
    public function update(User $user, Challenge $challenge): bool
    {
        return $user->isAdmin() || $challenge->id_creator == $user->id;
    }

    /**
     * Determine if the user can delete a challenge.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Challenge  $challenge
     * @return bool
     */
    public function delete(User $user, Challenge $challenge): bool
    {
        return $user->isAdmin() || $challenge->id_creator == $user->id;
    }

    /**
     * Determine if the user can participate in a challenge.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Challenge  $challenge
     * @return bool
     */
    public function participate(User $user, Challenge $challenge): bool
    {
        if ($challenge->private) {
            return $challenge->participants->contains($user->id);
        }
        return true; // Desafios públicos podem ser acessados por qualquer usuário
    }

    /**
     * Determine if the user can search for challenges.
     *
     * @param  \App\Models\User|null  $user
     * @return bool
     */
    public function search(?User $user): bool
    {
        return true; // Qualquer usuário pode pesquisar desafios
    }
}
