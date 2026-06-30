<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Group;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the group.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return bool
     */
    public function view(User $user, Group $group): bool
    {
        return $user !== null;
    }

    /**
     * Determine whether the user can create groups.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user !== null;
    }

    /**
     * Determine whether the user can update the group.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return bool
     */
    public function update(User $user, Group $group): bool
    {
        // The user can update if they are the owner or an admin.
        $isOwner = $group->owner->id === $user->id;
        return $isOwner || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the group.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return bool
     */
    public function delete(User $user, Group $group): bool
    {
        // The user can delete if they are the owner or an admin.
        $isOwner = $group->owner->id === $user->id;
        return $isOwner || $user->isAdmin();
    }

    /**
     * Determine whether the user can join the group.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return bool
     */
    public function join(User $user, Group $group): bool
    {
        return !$group->members->contains($user->id);
    }

    /**
     * Determine whether the user can leave the group.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return bool
     */
    public function leave(User $user, Group $group): bool
    {
        return $group->members->contains($user->id);
    }

    /**
     * Determine whether the user can invite other users to the group.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return bool
     */
    public function invite(User $user, Group $group): bool
    {
        // The owner or an admin can invite members to the group.
        $isOwner = $group->owner->id === $user->id;
        return $isOwner || $user->isAdmin();
    }

    /**
     * Determine whether the user can view group requests.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return bool
     */
    public function viewRequests(User $user, Group $group): bool
    {
        // Only the owner or an admin can view requests.
        $isOwner = $group->owner->id === $user->id;
        return $isOwner || $user->isAdmin();
    }
}
