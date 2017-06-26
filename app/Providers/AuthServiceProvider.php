<?php

namespace App\Providers;

use App\Auth\UserProvider;
use App\CommitteeRole;
use App\Election;
use App\ElectionNomination;
use App\EquipmentBreakage;
use App\Event;
use App\EventCrew;
use App\EventTime;
use App\Page;
use App\Policies\CommitteePolicy;
use App\Policies\Elections\ElectionPolicy;
use App\Policies\Elections\NominationPolicy;
use App\Policies\Equipment\RepairPolicy;
use App\Policies\Events\EventCrewPolicy;
use App\Policies\Events\EventPolicy;
use App\Policies\Events\EventTimePolicy;
use App\Policies\Members\UserPolicy;
use App\Policies\PagePolicy;
use App\Policies\QuotePolicy;
use App\Quote;
use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
        Event::class              => EventPolicy::class,
        EventCrew::class          => EventCrewPolicy::class,
        EventTime::class          => EventTimePolicy::class,
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
