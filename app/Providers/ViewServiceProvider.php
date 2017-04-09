<?php

namespace App\Providers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
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
        $this->attachSearchFilter();
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
     * Attach the search and filter values to all views.
     */
    private function attachSearchFilter()
    {
        View::composer('*', function ($view) {
            $filterValue = Request::has('filter') ? Request::get('filter') : null;
            $searchValue = Request::has('search') ? Request::get('search') : null;
            $route       = route(Route::currentRouteName(), Route::current()->parameters, true);
            $query       = Request::query();
            
            if(!is_null($filterValue)) {
                unset($query['filter']);
            }
            if(!is_null($searchValue)) {
                unset($query['search']);
            }
            
            $view->with('filterValue', $filterValue)
                 ->with('searchValue', $searchValue)
                 ->with('filterBaseUrl', $route)
                 ->with('filterBaseQuery', $query);
        });
    }
}
