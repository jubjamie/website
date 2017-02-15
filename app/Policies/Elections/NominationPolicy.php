<?php
    
    namespace App\Policies\Elections;
    
    use App\User;
    use App\ElectionNomination;
    use Illuminate\Auth\Access\HandlesAuthorization;
    
    class NominationPolicy
    {
        use HandlesAuthorization;
        
        /**
         * Determine whether the user can create nominations.
         * @param  \App\User $user
         * @return mixed
         */
        public function create(User $user)
        {
            return $user->isAdmin();
        }
        
        /**
         * Determine whether the user can view a manifesto.
         * @param \App\User               $user
         * @param \App\ElectionNomination $electionNomination
         * @return bool
         */
        public function manifesto(User $user, ElectionNomination $electionNomination)
        {
            return $user->isMember();
        }
        
        /**
         * Determine whether the user can delete nominations.
         * @param  \App\User               $user
         * @param  \App\ElectionNomination $electionNomination
         * @return mixed
         */
        public function delete(User $user, ElectionNomination $electionNomination)
        {
            return $user->isAdmin();
        }
    }
