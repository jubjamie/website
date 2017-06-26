<?php

namespace App\Policies\Events;

use App\EventCrew;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventCrewPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine if the user can update a crew entry.
     * @param \App\User      $user
     * @param \App\EventCrew $crew
     * @return bool
     */
    public function update(User $user, EventCrew $crew)
    {
        return $user->can('update', $crew->event);
    }
    
    /**
     * Determine if the user can delete a crew entry.
     * @param \App\User      $user
     * @param \App\EventCrew $crew
     * @return bool
     */
    public function delete(User $user, EventCrew $crew)
    {
        return $user->can('update', $crew->event);
    }
}
