<?php

namespace App\Policies\Resources;

use App\Resource;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResourcePolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user view the list of resources.
     * @param \App\User $user
     * @return bool
     */
    public function index(User $user)
    {
        return $user->isAdmin();
    }
    
    /**
     * Determine whether the user can create resources.
     * @param \App\User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }
    
    /**
     * Determine whether the user can update a resource.
     * @param \App\User $user
     * @param Resource  $resource
     * @return bool
     */
    public function update(User $user, Resource $resource)
    {
        return $user->isAdmin();
    }
    
    /**
     * Determine whether the user can delete a resource.
     * @param \App\User $user
     * @param Resource  $resource
     * @return bool
     */
    public function delete(User $user, Resource $resource)
    {
        return $user->isAdmin();
    }
}
