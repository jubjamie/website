<?php

namespace App\Policies\Resources;

use App\ResourceTag;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TagPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user view the list of resource tags.
     * @param \App\User $user
     * @return bool
     */
    public function index(User $user)
    {
        return $user->isAdmin();
    }
    
    /**
     * Determine whether the user can create resource tags.
     * @param \App\User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }
    
    /**
     * Determine whether the user can update a resource tag.
     * @param \App\User        $user
     * @param \App\ResourceTag $tag
     * @return bool
     */
    public function update(User $user, ResourceTag $tag)
    {
        return $user->isAdmin();
    }
    
    /**
     * Determine whether the user can delete a resource category.
     * @param \App\User        $user
     * @param \App\ResourceTag $tag
     * @return bool
     */
    public function delete(User $user, ResourceTag $tag)
    {
        return $user->isAdmin();
    }
}
