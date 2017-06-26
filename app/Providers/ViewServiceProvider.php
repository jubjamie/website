<?php

namespace App\Providers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
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
        $this->attachMemberEvents();
        $this->attachMemberSkills();
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
        view()->composer('app.messages.message', function ($view) {
            $view->with('MessageIcons', [
                'success' => 'check',
                'info'    => 'info',
                'warning' => 'exclamation',
                'danger'  => 'remove',
            ]);
        });
    }
    
    /**
     * Attach the list of events for the given member.
     */
    private function attachMemberEvents()
    {
        view()->composer('members.profile.events', function ($view) {
            $user = $view->getData()['user'];
            
            $events = $user->events()
                           ->distinctPaginate(20)
                           ->withPath(route(Route::currentRouteName(), Route::current()->parameters + ['tab' => 'events']));
            
            $view->with([
                'events' => $events,
            ]);
        });
    }
    
    /**
     * Attach the list of skills.
     */
    private function attachMemberSkills()
    {
        view()->composer('members.profile.training', function ($view) {
            $view->with([
                'skill_categories' => [],
            ]);
        });
    }
}
