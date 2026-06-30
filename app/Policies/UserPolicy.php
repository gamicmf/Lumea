<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the authenticated user can view the given user's profile.
     *
     * @param  \App\Models\User  $authUser
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function view(User $authUser, User $user)
    {
        // All users can view other users' profiles
        return true;
    }

    /**
     * Determine if the authenticated user can update the given user's profile.
     *
     * @param  \App\Models\User  $authUser
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function update(User $authUser, User $user)
    {
        // Only the user themselves or an admin can update the profile
        return $authUser->id === $user->id || $authUser->isAdmin();
    }

    /**
     * Determine if the authenticated user can delete the given user's account.
     *
     * @param  \App\Models\User  $authUser
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function delete(User $authUser, User $user)
    {
        // Only the user themselves or an admin can delete the account
        return $authUser->id === $user->id || $authUser->isAdmin();
    }

    /**
     * Determine if the authenticated user can search for other users.
     *
     * @param  \App\Models\User  $authUser
     * @return bool
     */
    public function search(User $authUser)
    {
        // Any authenticated user can search for other users
        return true;
    }
}
