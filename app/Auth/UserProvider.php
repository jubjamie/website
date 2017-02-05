<?php
    namespace App\Auth;
    
    use Illuminate\Auth\EloquentUserProvider;
    use Illuminate\Contracts\Auth\Authenticatable;
    use Illuminate\Support\Facades\Auth;
    use Szykra\Notifications\Flash;
    
    class UserProvider extends EloquentUserProvider
    {
        /**
         * Track whether the error message has been created or not.
         * @var bool
         */
        private static $MessageCreated = false;
        
        /**
         * Override the Eloquent method to check the account is enabled.
         * @param  mixed  $identifier
         * @param  string $token
         * @return \Illuminate\Contracts\Auth\Authenticatable|null
         */
        public function retrieveByToken($identifier, $token)
        {
            return $this->checkUserStatus(parent::retrieveByToken($identifier, $token));
        }
        
        /**
         * Override the Eloquent method to check the account is enabled.
         * @param array $credentials
         * @return \Illuminate\Contracts\Auth\Authenticatable|null
         */
        public function retrieveByCredentials(array $credentials)
        {
            return $this->checkUserStatus(parent::retrieveByCredentials($credentials));
        }
        
        /**
         * Override the Eloquent method to check the account is enabled.
         * @param mixed $id
         * @return \Illuminate\Contracts\Auth\Authenticatable|null
         */
        public function retrieveById($id)
        {
            return $this->checkUserStatus(parent::retrieveById($id));
        }
        
        /**
         * Method to check the status of the user account and create
         * the error message if the user's account is disabled.
         * @param  $user
         * @return \Illuminate\Contracts\Auth\Authenticatable|null
         */
        private function checkUserStatus($user)
        {
            if($user && !$user->status) {
                session()->flush();
                session()->regenerate();
                
                if(!self::$MessageCreated) {
                    Flash::error('Account disabled', 'Please <a href="mailto:sec@bts-crew.com">contact the secretary</a> to have your account re-enabled.');
                    self::$MessageCreated = true;
                }
                
                return null;
            }
            
            return $user;
        }
    }