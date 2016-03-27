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
		$this->composeSubMenus();
	}
	
	/**
	 * Register the application services.
	 * @return void
	 */
	public function register()
	{
		//
	}
	
	private function composeMainMenu()
	{
		View::composer('app', function ($view) {
			// Set up some permissions
			$user         = Auth::user();
			$isRegistered = !!$user;
			$isMember     = $isRegistered && $user->isMember();
			$isCommittee  = $isRegistered && $user->isCommittee();
			$isAssociate  = $isRegistered && $user->isAssociate();
			$isSU         = $isRegistered && $user->isSU();
			$isAdmin      = $isRegistered && $user->isAdmin();
			
			
			// Create the parent menu
			$menu = Menu::handler('mainNav');
			$menu->add(route('home'), 'Home')->activePattern('\/page\/home');
			$menu->add(route('page.show', 'about'), 'About Us');
			$menu->add(route('committee.view'), 'The Committee');
			$menu->add(route('gallery.index'), 'Galleries')->activePattern('\/gallery');
			$menu->add(route('members.dash'), 'Members\' Area', Menu::items('members'))->activePattern('\/members');
			$menu->add(route('resources.search'), 'Resources', Menu::items('resources'))->activePattern('\/resources');
			$menu->add(route('contact.enquiries'), 'Enquiries');
			$menu->add(route('contact.book'), 'Book Us')->activePattern('\/contact\/book');
			
			// Build the members sub-menu
			if($isRegistered) {
				$members = $menu->find('members');
				$members->add(route('members.myprofile'), 'My Profile', Menu::items('members.profile'), [], ['class' => 'profile'])
				        ->add(route('events.diary'), 'Events Diary', Menu::items('members.events'), [], ['class' => 'events'])
				        ->activePattern('\/events\/diary');
				
				if($isMember || $isAdmin) {
					$members->add(route('membership'), 'The Membership', Menu::items('members.users'), [], ['class' => 'admin-users'])
					        ->add(route('quotes.index'), 'Quotes Board')
					        ->add(route('equipment.dash'), 'Equipment', Menu::items('members.equipment'), [], ['class' => 'equipment'])
					        ->add(route('training.dash'), 'Training', Menu::items('members.training'), [], ['class' => 'training'])
					        ->add('#', 'Other', Menu::items('members.misc'), [], ['class' => 'misc'])
					        ->raw('', null, ['class' => 'divider'])
					        ->add(route('contact.accident'), 'Report an Accident')
					        ->raw('', null, ['class' => 'divider']);
					
					// Build the profile sub-menu
					$menu->find('members.profile')
					     ->add(route('members.myprofile', 'events'), 'My events')
					     ->add(route('members.myprofile', 'training'), 'My training');
					
					// Build the events sub-menu
					$events = $menu->find('members.events');
					$events->add(route('events.mydiary'), 'My diary')->activePattern('\/events\/my-diary')
					       ->add(route('events.signup'), 'Event sign-up')->activePattern('\/events\/signup')
					       ->add('https://docs.google.com/forms/d/1kSY107KrDb-ZGpiRCppeSK9lA8kCA8Kgz5TFd9nEVXo/viewform', 'Submit event report');
					if($isAdmin) {
						//$events->add('#', 'View booking requests')
						$events->add(route('events.index'), 'View all events')
						       ->add(route('events.add'), 'Add event');
					}
					
					// Build the users sub-menu
					if($isAdmin) {
						$menu->find('members.users')
						     ->add(route('user.index'), 'View all users')
						     ->add(route('user.create'), 'Create a new user');
					}
					
					// Build the equipment sub-menu
					$menu->find('members.equipment')
					     ->add(route('equipment.assets'), 'Asset register')
					     ->add(route('equipment.repairs'), 'View repairs db')
					     ->add(route('equipment.repairs.add'), 'Report broken kit');
					
					
					// Build the training sub-menu
					$training = $menu->find('members.training');
					$training->add(route('training.skills.index'), 'View skills');
					if($isAdmin) {
						$training->add(route('training.skills.proposal.index'), 'Review proposals')->activePattern('\/training\/skills\/proposal');
						$training->add(route('training.skills.log'), 'Skills log');
					}
					
					// Build the misc sub-menu
					$misc = $menu->find('members.misc');
					$misc
						->add(route('polls.index'), 'Polls')->activePattern('\/polls')
						->add(route('elections.index'), 'Committee elections')->activePattern('\/elections');
					//->add('#', 'BTS Awards')
					if($isAdmin) {
						$misc->add(route('page.index'), 'Manage webpages')
						     ->add(route('su.dash'), 'View SU Area');
					}
					
				}
				$members->add(route('auth.logout'), 'Log out');
			}
			
			// Build the resources sub-menu
			$resources = $menu->find('resources');
			if($isMember || $isAdmin) {
				$resources->add(route('resources.search', ['category' => 'event-reports']), 'Event Reports')
				          ->add(route('resources.search', ['category' => 'risk-assessments']), 'Risk Assessments')
				          ->add(route('resources.search', ['category' => 'meeting-minutes']), 'Meeting Minutes')
				          ->add(route('resources.search', ['category' => 'meeting-agendas']), 'Meeting Agendas');
			}
			//$resources->add('#', 'Safety Information')
			//->add('#', 'Weather Forecast')
			$resources->add(route('page.show', 'links'), 'Links')
			          ->add(route('page.show', 'faq'), 'FAQ');
			
			
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
			
			$view->with('mainNav', $menu->render());
		});
	}
	
	private function composeSubMenus()
	{
		// Compose the contact sub-menu
		View::composer('contact.shared', function ($view) {
			$menu = Menu::handler('contactMenu');
			$menu->add(route('contact.enquiries'), 'General Enquiries')
			     ->add(route('contact.book'), 'Book Us')
			     ->add(route('contact.feedback'), 'Provide Feedback');
			$menu->addClass('nav nav-tabs');
			$view->with('menu', $menu->render());
		});
		
		// Compose the profile sub-menu
		View::composer('members.profile', function ($view) {
			$username = $view->getData()['user']->username;
			$menu     = Menu::handler('profileMenu');
			$menu->add(route('members.profile', $username), 'Details')
			     ->add(route('members.profile', ['username' => $username, 'tab' => 'events']), 'Events')
			     ->add(route('members.profile', ['username' => $username, 'tab' => 'training']), 'Training');
			$menu->addClass('nav nav-tabs');
			$view->with('menu', $menu->render());
		});
		
		// Compose the 'my profile' sub-menu
		View::composer('members.my_profile', function ($view) {
			$menu = Menu::handler('profileMenu');
			$menu->add(route('members.myprofile'), 'My Details')
			     ->add(route('members.myprofile', 'events'), 'Events')
			     ->add(route('members.myprofile', 'training'), 'Training');
			$menu->addClass('nav nav-tabs');
			$view->with('menu', $menu->render());
		});
		
		// Compose the signup sub-menu
		View::composer('events.signup', function ($view) {
			$menu = Menu::handler('signupMenu');
			$menu->add(route('events.signup', 'em'), 'Requiring an EM')->activePattern('\/events\/signup$')
			     ->add(route('events.signup', 'crew'), 'Requiring Crew');
			$menu->addClass('nav nav-tabs');
			$view->with('menu', $menu->render());
		});
	}
}
