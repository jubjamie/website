<?php

namespace App\Policies;

use App\User;
use App\CommitteeRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommitteePolicy
{
    use HandlesAuthorization;

        /**
     * Determine whether the user can create committeeRoles.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the committeeRole.
     *
     * @param  \App\User  $user
     * @param  \App\CommitteeRole  $committeeRole
     * @return mixed
     */
    public function update(User $user, CommitteeRole $committeeRole)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the committeeRole.
     *
     * @param  \App\User  $user
     * @param  \App\CommitteeRole  $committeeRole
     * @return mixed
     */
    public function delete(User $user, CommitteeRole $committeeRole)
    {
        return $user->isAdmin();
    }
}
