<?php

namespace App\Policies\Events;

use App\Event;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user view the list of events.
     * @param \App\User $user
     * @return bool
     */
    public function index(User $user)
    {
        return $user->isAdmin();
    }
    
    /**
     * Determine whether the user can create events.
     * @param \App\User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }
    
    /**
     * Determine whether the user can update events.
     * @param \App\User  $user
     * @param \App\Event $event
     * @return bool
     */
    public function update(User $user, Event $event)
    {
        return $user->isAdmin() || $user->hasEMRole($event);
    }
    
    /**
     * Determine whether the user can volunteer for an event.
     * @param \App\User  $user
     * @param \App\Event $event
     * @return bool
     */
    public function volunteer(User $user, Event $event)
    {
        return $event->isCrewListOpen() && $user->isMember() && !$event->isTEM($user);
    }
    
    /**
     * Determine whether the user can delete events.
     * @param \App\User $user
     * @return bool
     */
    public function delete(User $user)
    {
        return $user->isAdmin();
    }
}
