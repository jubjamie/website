<?php
    
    namespace App\Providers;
    
    use App\Auth\UserProvider;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Gate;
    use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
    
    class AuthServiceProvider extends ServiceProvider
    {
        /**
         * The policy mappings for the application.
         * @var array
         */
        protected $policies = [
        ];
        
        /**
         * Register any authentication / authorization services.
         * @return void
         */
        public function boot()
        {
            $this->registerPolicies();
            $this->registerAuthGates();
            
            // Tell Laravel to use the custom UserProvider
            Auth::provider('eloquent', function ($app, array $config) {
                return new UserProvider($app['hash'], $config['model']);
            });
        }
        
        /**
         * Register any general Gates used for authorisation.
         */
        public function registerAuthGates()
        {
            Gate::define('global.write', function ($user) {
                return $user->isAdmin();
            });
            Gate::define('members.strict', function ($user) {
                return $user->isMember();
            });
        }
    }
