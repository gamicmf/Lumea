<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Notification;

class NotificationsPolicy
{
    /**
     * Determine se o usuário pode rejeitar um convite para o grupo.
     *
     * @param User $user
     * @param Notification $notification
     * @return bool
     */
    public function rejectInvite(User $user, Notification $notification)
    {
        // O usuário pode rejeitar o convite se for o destinatário da notificação
        return $notification->received_user === $user->id;
    }

    /**
     * Determine se o usuário pode marcar uma notificação como visualizada.
     *
     * @param User $user
     * @param Notification $notification
     * @return bool
     */
    public function markAsViewed(User $user, Notification $notification)
    {
        // Apenas o destinatário da notificação pode marcá-la como visualizada
        return $notification->received_user === $user->id;
    }
}
