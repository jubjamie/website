<?php
    
    namespace App\Providers;
    
    use App\User;
    use Illuminate\Support\Facades\View;
    use Illuminate\Support\ServiceProvider;
    
    class ViewServiceProvider extends ServiceProvider
    {
        /**
         * Bootstrap the application services.
         * @return void
         */
        public function boot()
        {
            $this->attachMessageStyles();
            $this->attachActiveUserList();
            $this->attachActiveMemberList();
        }
        
        /**
         * Register the application services.
         * @return void
         */
        public function register()
        {
            //
        }
        
        /**
         * Attach the icons for each type of flash message.
         */
        private function attachMessageStyles()
        {
            View::composer('app.messages.message', function ($view) {
                $view->with('MessageIcons', [
                    'success' => 'check',
                    'info'    => 'info',
                    'warning' => 'exclamation',
                    'danger'  => 'remove',
                ]);
            });
        }
    
        /**
         * Attach the list of active users for use in a <select> field.
         */
        private function attachActiveUserList()
        {
            View::composer([
                'pages.form'
            ], function ($view) {
                $users = User::active()
                             ->nameOrder()
                             ->getSelect();
                
                $view->with('ActiveUsers', $users);
            });
        }
    
        /**
         * Attach the list of active members for use in a <select> field.
         */
        private function attachActiveMemberList()
        {
            View::composer([
                
            ], function ($view) {
                $members = User::active()
                               ->member()
                               ->nameOrder()
                               ->getSelect();
                
                $view->with('ActiveMembers', $members);
            });
        }
    }
