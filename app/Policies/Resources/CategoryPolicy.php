<?php

namespace App\Policies\Resources;

use App\ResourceCategory;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user view the list of resource categories.
     * @param \App\User $user
     * @return bool
     */
    public function index(User $user)
    {
        return $user->isAdmin();
    }
    
    /**
     * Determine whether the user can create resource categories.
     * @param \App\User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }
    
    /**
     * Determine whether the user can update a resource category.
     * @param \App\User             $user
     * @param \App\ResourceCategory $category
     * @return bool
     */
    public function update(User $user, ResourceCategory $category)
    {
        return $user->isAdmin();
    }
    
    /**
     * Determine whether the user can delete a resource category.
     * @param \App\User             $user
     * @param \App\ResourceCategory $category
     * @return bool
     */
    public function delete(User $user, ResourceCategory $category)
    {
        return $user->isAdmin();
    }
}
