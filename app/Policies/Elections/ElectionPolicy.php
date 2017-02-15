<?php
    
    namespace App\Policies\Elections;
    
    use App\User;
    use App\Election;
    use Illuminate\Auth\Access\HandlesAuthorization;
    
    class ElectionPolicy
    {
        use HandlesAuthorization;
        
        /**
         * Determine whether the user can view the election list.
         * @param  \App\User $user
         * @return mixed
         */
        public function index(User $user)
        {
            return $user->isMember();
        }
        
        /**
         * Determine whether the user can view the election.
         * @param  \App\User     $user
         * @param  \App\Election $election
         * @return mixed
         */
        public function view(User $user, Election $election)
        {
            return $user->isMember();
        }
        
        /**
         * Determine whether the user can create elections.
         * @param  \App\User $user
         * @return mixed
         */
        public function create(User $user)
        {
            return $user->isAdmin();
        }
        
        /**
         * Determine whether the user can update the election.
         * @param  \App\User     $user
         * @param  \App\Election $election
         * @return mixed
         */
        public function update(User $user, Election $election)
        {
            return $user->isAdmin();
        }
        
        /**
         * Determine whether the user can delete the election.
         * @param  \App\User     $user
         * @param  \App\Election $election
         * @return mixed
         */
        public function delete(User $user, Election $election)
        {
            return $user->isAdmin();
        }
    }
