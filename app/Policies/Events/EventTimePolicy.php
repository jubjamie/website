<?php

namespace App\Policies\Events;

use App\EventTime;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventTimePolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine if the user can update a crew entry.
     * @param \App\User      $user
     * @param \App\EventTime $time
     * @return bool
     */
    public function update(User $user, EventTime $time)
    {
        return $user->can('update', $time->event);
    }
    
    /**
     * Determine if the user can delete a crew entry.
     * @param \App\User      $user
     * @param \App\EventTime $time
     * @return bool
     */
    public function delete(User $user, EventTime $time)
    {
        return $user->can('update', $time->event);
    }
}
