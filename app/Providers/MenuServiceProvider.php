<?php
    
    namespace App\Providers;
    
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\View;
    use Illuminate\Support\ServiceProvider;
    use Menu\Items\Contents\Link;
    use Menu\Menu;
    
    class MenuServiceProvider extends ServiceProvider
    {
        /**
         * Bootstrap the application services.
         * @return void
         */
        public function boot()
        {
            $this->composeMainMenu();
            $this->composeContactMenu();
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
         * Make the main menu.
         */
        private function composeMainMenu()
        {
            // TODO: Correct access once gates/policies are implemented
            View::composer('app.includes.menu', function ($view) {
                // Set up some permissions
                $user         = Auth::user();
                $isRegistered = Auth::check();
                $isMember     = $isRegistered && $user->isMember();
                $isStaff      = $isRegistered && $user->isStaff();
                $isAdmin      = $isRegistered && $user->isAdmin();
                
                // Create the parent menu
                $menu = Menu::handler('mainMenu');
                
                // Parent menu
                $menu->add(route('home'), 'Home');
                $menu->add(route('page.show', ['slug' => 'about']), 'About Us');
                $menu->add('#', 'Media', Menu::items('media'))->activePattern('\/media');
                $menu->add(route('committee.view'), 'The Committee');
                $menu->add('#', 'Events Diary', Menu::items('events'))->activePattern('\/events');
                $menu->add(route('auth.login'), 'Members\' Area', Menu::items('members'))->activePattern('\/members');
                $menu->add('#', 'Resources', Menu::items('resources'))->activePattern('\/resources');
                $menu->add(route('contact.book'), 'Enquiries & Book Us')->activePattern('\/contact\/book');
                
                // Media sub-menu
                $media = $menu->find('media');
                $media->add(route('media.images.index'), 'Image Gallery')->activePattern('\/media\/images')
                      ->add(route('media.videos.index'), 'Videos')->activePattern('\/media\/videos');
                
                if($isRegistered) {
                    // Events sub-menu
                    $menu->find('events')
                         ->add('#', 'My diary')->activePattern('\/events\/my-diary')
                         ->add('#', 'Event sign-up')->activePattern('\/events\/signup')
                         ->add('https://docs.google.com/a/bts-crew.com/forms/d/e/1FAIpQLSekw6oEojBdD1REd2krli3U-4BYWNG9zfThCmTJKc1A1OaR3g/viewform',
                             'Submit event report')
                         ->add('#', 'View booking requests')
                         ->add('#', 'View all events')
                         ->add('#', 'Add event');
                    
                    // Members' area sub-menu
                    $menu->find('members')
                         ->add('#', 'My Profile', Menu::items('members.profile'), [], ['class' => 'profile'])
                         ->add('#', 'The Membership', Menu::items('members.users'), [], ['class' => 'admin-users'])
                         ->add(route('quotes.index'), 'Quotes Board')
                         ->add('#', 'Equipment', Menu::items('members.equipment'), [], ['class' => 'equipment'])
                         ->add('#', 'Training', Menu::items('members.training'), [], ['class' => 'training'])
                         ->add('#', 'Other', Menu::items('members.misc'), [], ['class' => 'misc'])
                         ->raw('', null, ['class' => 'divider'])
                         ->add(route('contact.accident'), 'Report an Accident')
                         ->raw('', null, ['class' => 'divider'])
                         ->add(route('auth.logout'), 'Log out');
                    
                    // Profile sub-menu
                    $menu->find('members.profile')
                         ->add('#', 'My events')
                         ->add('#', 'My training');
                    
                    // Users sub-menu
                    $menu->find('members.users')
                         ->add('#', 'View all users')
                         ->add('#', 'Create a new user');
                    
                    // Equipment sub-menu
                    $menu->find('members.equipment')
                         ->add('#', 'Asset register')
                         ->add('#', 'View repairs db')
                         ->add('#', 'Report broken kit');
                    
                    // Training sub-menu
                    $menu->find('members.training')
                         ->add('#', 'View skills')
                         ->add('#', 'Review proposals')->activePattern('\/training\/skills\/proposal')
                         ->add('#', 'Skills log');
                    
                    // Other sub-menu
                    $menu->find('members.misc')
                         ->add('#', 'Committee elections')->activePattern('\/elections')
                         ->add('#', 'BTS Awards')
                         ->add(route('page.index'), 'Manage webpages')
                         ->add('#', 'View SU Area');
                }
                
                // Resources sub-menu
                $resources = $menu->find('resources');
                if($isRegistered) {
                    $resources->add('#', 'Event Reports')
                              ->add('#', 'Event Risk Assessments')
                              ->add('#', 'Meeting Minutes')
                              ->add('#', 'Meeting Agendas');
                }
                $resources->add(route('page.show', ['slug' => 'links']), 'Links')
                          ->add(route('page.show', ['slug' => 'faq']), 'FAQ');
                
                // Add the necessary classes
                $menu->addClass('nav navbar-nav')
                     ->getItemsByContentType(Link::class)
                     ->map(function ($item) {
                         if($item->hasChildren()) {
                             $item->addClass('dropdown');
                             $item->getChildren()->getAllItems()->map(function ($childItem) use ($item) {
                                 if($childItem->isActive()) {
                                     $item->addClass('active');
                                 }
                             });
                         }
                     });
                $menu->getAllItemLists()
                     ->map(function ($itemList) {
                         if($itemList->hasChildren()) {
                             $itemList->addClass('dropdown-menu');
                         }
                     });
                
                // Render
                $view->with('mainMenu', $menu->render());
            });
        }
    
        /**
         * Make the sub menu for the contact section.
         */
        private function composeContactMenu()
        {
            View::composer('contact.shared', function ($view) {
                $menu = Menu::handler('contactMenu');
                $menu->add(route('contact.enquiries'), 'General Enquiries')
                     ->add(route('contact.book'), 'Book Us')->activePattern('\/contact\/book')
                     ->add(route('contact.feedback'), 'Provide Feedback');
                $menu->addClass('nav nav-tabs');
                $view->with('menu', $menu->render());
            });
        }
    }
