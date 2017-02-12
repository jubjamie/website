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
    }
