<?php
    
    namespace App\Policies;
    
    use App\Page;
    use App\User;
    use bnjns\FlashNotifications\Facades\Notifications;
    use Illuminate\Auth\Access\HandlesAuthorization;

    class PagePolicy
    {
        use HandlesAuthorization;
    
        /**
         * Determine whether the user can view the page index.
         * @param  \App\User $user
         * @return mixed
         */
        public function index(User $user)
        {
            return $user->isAdmin();
        }
        
        /**
         * Determine whether the user can view the page.
         * @param  \App\User $user
         * @param  \App\Page $page
         * @return mixed
         */
        public function view(User $user, Page $page)
        {
            if($page->published == 1) {
                return true;
            } else if($user->isAdmin()) {
                Notifications::warning('This page will not be viewable by non-admins until it is published.', 'Page not published');
                return true;
            }
            
            return false;
        }
        
        /**
         * Determine whether the user can create pages.
         * @param  \App\User $user
         * @return mixed
         */
        public function create(User $user)
        {
            return $user->isAdmin();
        }
        
        /**
         * Determine whether the user can update the page.
         * @param  \App\User $user
         * @param  \App\Page $page
         * @return mixed
         */
        public function update(User $user, Page $page)
        {
            return $user->isAdmin();
        }
        
        /**
         * Determine whether the user can delete the page.
         * @param  \App\User $user
         * @param  \App\Page $page
         * @return mixed
         */
        public function delete(User $user, Page $page)
        {
            return $user->isAdmin();
        }
    }
