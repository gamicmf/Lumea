<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Publication;
use App\Models\Posts;
use Illuminate\Auth\Access\HandlesAuthorization;

class PublicationPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user !== null;
    }
    public function view(User $user, Publication $publication)
    {
        return $user !== null;
    }

    public function update(User $user, Publication $publication)
    {
        return $user->id === $publication->post->user->id || $user->isAdmin();
    }

    public function delete(User $user, Publication $publication): bool
    {
        return $user->id === $publication->post->user->id || $user->isAdmin();
    }

    // Outras políticas de autorização...
}