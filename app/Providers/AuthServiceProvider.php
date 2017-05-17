<?php

namespace App\Providers;

use App\Auth\UserProvider;
use App\CommitteeRole;
use App\Election;
use App\ElectionNomination;
use App\EquipmentBreakage;
use App\Page;
use App\Policies\CommitteePolicy;
use App\Policies\Elections\NominationPolicy;
use App\Policies\Elections\ElectionPolicy;
use App\Policies\Equipment\RepairPolicy;
use App\Policies\Members\UserPolicy;
use App\Policies\PagePolicy;
use App\Policies\QuotePolicy;
use App\Quote;
use App\User;
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
        EquipmentBreakage::class  => RepairPolicy::class,
        CommitteeRole::class      => CommitteePolicy::class,
        Election::class           => ElectionPolicy::class,
        ElectionNomination::class => NominationPolicy::class,
        Page::class               => PagePolicy::class,
        Quote::class              => QuotePolicy::class,
        User::class               => UserPolicy::class,
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
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });
        Gate::define('member', function ($user) {
            return $user->isMember();
        });
    }
}
