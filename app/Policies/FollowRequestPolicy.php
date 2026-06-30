<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FollowRequest;

class FollowRequestPolicy
{
    /**
     * Determine se o usuário pode visualizar a solicitação de seguir.
     *
     * @param User $user
     * @param FollowRequest $followRequest
     * @return bool
     */
    public function view(User $user, FollowRequest $followRequest)
    {
        // Permitir apenas para o seguidor ou o usuário seguido
        return $user->id === $followRequest->id_follower || $user->id === $followRequest->id_followed;
    }

    /**
     * Determine se o usuário pode criar uma solicitação de seguir.
     *
     * @param User $user
     * @param User $targetUser
     * @return bool
     */
    public function create(User $user, User $targetUser)
    {
        // Um usuário não pode seguir a si mesmo ou enviar outra solicitação se já existir
        return $user->id !== $targetUser->id &&
            !FollowRequest::where('id_follower', $user->id)
                ->where('id_followed', $targetUser->id)
                ->exists();
    }

    /**
     * Determine se o usuário pode cancelar uma solicitação de seguir.
     *
     * @param User $user
     * @param FollowRequest $followRequest
     * @return bool
     */
    public function delete(User $user, FollowRequest $followRequest)
    {
        // Apenas o seguidor pode cancelar a solicitação
        return $user->id === $followRequest->id_follower;
    }
}
