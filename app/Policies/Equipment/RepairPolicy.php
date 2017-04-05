<?php

namespace App\Policies\Equipment;

use App\EquipmentBreakage;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RepairPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine if the user can view the list of breakages.
     * @param \App\User $user
     * @return bool
     */
    public function index(User $user)
    {
        return $user->isMember();
    }
    
    /**
     * Determine whether the user can view the equipment breakage.
     * @param  \App\User             $user
     * @param \App\EquipmentBreakage $equipmentBreakage
     * @return mixed
     */
    public function view(User $user, EquipmentBreakage $equipmentBreakage)
    {
        return $user->isMember();
    }
    
    /**
     * Determine whether the user can create an equipment breakage.
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isMember();
    }
    
    /**
     * Determine whether the user can update the equipment breakage.
     * @param  \App\User             $user
     * @param \App\EquipmentBreakage $equipmentBreakage
     * @return mixed
     */
    public function update(User $user, EquipmentBreakage $equipmentBreakage)
    {
        return $user->isAdmin();
    }
}
