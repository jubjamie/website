<?php

namespace App\Providers;

use App\Resource;
use App\ResourceCategory;
use App\ResourceTag;
use App\Traits\CorrectsPaginatorPath;
use bnjns\FlashNotifications\Facades\Notifications;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    use CorrectsPaginatorPath;
    
    /**
     * Bootstrap the application services.
     * @return void
     */
    public function boot()
    {
        $this->attachNotifications();
        $this->attachMemberEvents();
        $this->attachMemberSkills();
        $this->attachResourceVariables();
        $this->attachResourceSearchVariables();
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
     * Attach some default notifications.
     */
    private function attachNotifications()
    {
        // Javascript
        Notifications::warning('We use javascript to improve the user experience and make things more interactive - things may not work if you have javascript turned off.')
                     ->title('Uh oh! No javascript!')
                     ->enclose('noscript')
                     ->permanent();
        
        // Cookie policy
        if(!isset($_COOKIE['CookiePolicyAccepted'])) {
            Notifications::info('Some rubbish about our cookie policy with a [link to the policy](#).')
                         ->title('Cookie policy')
                         ->attribute('id', 'cookie-policy-msg')
                         ->permanent();
        }
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
                           ->withPath($this->paginatorPath(['tab' => 'events']));
            
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
    
    /**
     * Attach the variables for the resources section
     */
    private function attachResourceVariables()
    {
        // Access levels
        view()->composer('resources.*', function ($view) {
            $view->with([
                'AccessLevels'       => Resource::ACCESS,
                'ResourceCategories' => ResourceCategory::orderBy('name', 'ASC')->get(),
                'ResourceTags'       => ResourceTag::orderBy('name', 'ASC')->get(),
                'ResourceTypes'      => Resource::TYPES,
            ]);
        });
    }
    
    /**
     * Attach the variables to the search results view.
     */
    private function attachResourceSearchVariables()
    {
        view()->composer('resources.search.results', function ($view) {
            $resources = $view->getData()['resources'];
            $search    = $view->getData()['search'];
            
            // Result counts
            $counts = [
                'lower' => ($resources->currentPage() - 1) * $resources->perPage() + 1,
                'upper' => min(($resources->currentPage()) * $resources->perPage(), $resources->total()),
                'total' => $resources->total(),
            ];
            
            // Categories
            $categories    = ResourceCategory::orderBy('name', 'ASC')->get();
            $request       = Request::except('page', 'category');
            $category_list = [];
            foreach($categories as $category) {
                $category_list[] = (object) [
                    'name'    => $category->name,
                    'link'    => route('resource.search', $request + ['category' => $category->slug]),
                    'current' => $search->category && $search->category == $category->slug,
                ];
            }
            
            // Tags
            $tags     = ResourceTag::orderBy('name', 'ASC')->get();
            $request  = Request::except('page', 'tag');
            $tag_list = [];
            foreach($tags as $tag) {
                if(in_array($tag->slug, $search->tags)) {
                    $tag_param = array_filter($search->tags, function ($slug) use ($tag) {
                        return $slug != $tag->slug;
                    });
                } else {
                    $tag_param = array_merge($search->tags, [$tag->slug]);
                }
                
                $tag_list[] = (object) [
                    'name'    => $tag->name,
                    'link'    => route('resource.search', $request + ['tag' => $tag_param]),
                    'current' => in_array($tag->slug, $search->tags),
                ];
            }
            
            $view->with([
                'Counts'       => $counts,
                'CategoryList' => $category_list,
                'TagList'      => $tag_list,
            ]);
        });
    }
}
