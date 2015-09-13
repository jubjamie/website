<?php

namespace App\Providers;

use App\Event;
use App\TrainingCategory;
use App\TrainingSkill;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Menu\Items\Contents\Link;
use Menu\Menu;

class ViewServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 * @return void
	 */
	public function boot()
	{
		$this->composeFlash();
		$this->composeMainMenu();
		$this->composeSubMenus();
		$this->attachUserList();
		$this->attachMemberList();
		$this->attachMemberEvents();
		$this->attachMemberSkills();
		$this->attachActiveUser();
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
	 * Attach a composer to the flash view so that the view has access
	 * to the FA icons for each alert level.
	 */
	private function composeFlash()
	{
		// Attach the FA icons for flash messages
		View::composer('partials.flash.message', function ($view) {
			$view->with('flashIcons', [
				'success' => 'check',
				'info'    => 'info',
				'warning' => 'exclamation',
				'danger'  => 'remove',
			]);
		});
	}

	/**
	 * Build the main navigation bar.
	 */
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
			$menu->add('#', 'Resources', Menu::items('resources'));
			$menu->add(route('contact.enquiries'), 'Enquiries');
			$menu->add(route('contact.book'), 'Book Us')->activePattern('\/contact\/book');

			// Build the members sub-menu
			if($isRegistered) {
				$members = $menu->find('members');

				if($isMember || $isAdmin) {
					$members->add(route('members.myprofile'), 'My Profile', Menu::items('members.profile'), [], ['class' => 'profile'])
					        ->add(route('events.diary'), 'Events Diary', Menu::items('members.events'), [], ['class' => 'events'])
					        ->activePattern('\/events\/diary')
					        ->add(route('membership'), 'The Membership', Menu::items('members.users'), [], ['class' => 'admin-users'])
					        ->add(route('quotes.index'), 'Quotes Board')
					        ->add(route('equipment.dash'), 'Equipment', Menu::items('members.equipment'), [], ['class' => 'equipment'])
					        ->add(route('training.dash'), 'Training', Menu::items('members.training'), [], ['class' => 'training'])
					        ->add(route('polls.index'), 'Polls')->activePattern('\/polls')
						//->raw('', null, ['class' => 'divider'])
						//->add('#', 'Elections Home')
						//->add('#', 'BTS Awards')
						    ->raw('', null, ['class' => 'divider']);
					if($isAdmin) {
						$members->add('#', 'Miscellaneous', Menu::items('members.misc'), [], ['class' => 'misc'])
						        ->raw('', null, ['class' => 'divider'])
						        ->add(route('su.dash'), 'View SU Area')
						        ->raw('', null, ['class' => 'divider']);
					}
					$members->add(route('contact.accident'), 'Report an Accident')
					        ->raw('', null, ['class' => 'divider'])
					        ->add(route('auth.logout'), 'Logout');

					// Build the profile sub-menu
					$menu->find('members.profile')
					     ->add(route('members.myprofile') . '#training', 'My training')
					     ->add(route('members.myprofile') . '#events', 'My events');

					// Build the events sub-menu
					$events = $menu->find('members.events');
					$events->add(route('events.mydiary'), 'My diary')->activePattern('\/events\/my-diary')
					       ->add(route('events.signup'), 'Event sign-up')->activePattern('\/events\/signup');
					//->add('#', 'Submit event report');
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
					if($isAdmin) {
						$misc = $menu->find('members.misc');
						$misc->add(route('page.index'), 'Manage webpages');
					}

				}
			}

			// Build the resources sub-menu
			$resources = $menu->find('resources');
			if($isMember || $isAdmin) {
				//$resources->add('#', 'Event Reports')
				//          ->add('#', 'Risk Assessments')
				//          ->add('#', 'Meeting Minutes')
				//          ->add('#', 'Meeting Agendas');
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

	/**
	 * Create all the necessary sub-menus
	 */
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
			$menu->add(route('members.profile', $username), 'Details', null, [], ['id' => 'profileTab'])
			     ->add(route('members.profile', $username) . '#events', 'Events', null, [], ['id' => 'eventsTab'])
			     ->add(route('members.profile', $username) . '#training', 'Training', null, [], ['id' => 'trainingTab']);
			$menu->addClass('nav nav-tabs');
			$view->with('menu', $menu->render());
		});

		// Compose the 'my profile' sub-menu
		View::composer('members.my_profile', function ($view) {
			$menu = Menu::handler('profileMenu');
			$menu->add(route('members.myprofile'), 'My Details', null, [], ['id' => 'profileTab'])
			     ->add(route('members.myprofile') . '#events', 'Events', null, [], ['id' => 'eventsTab'])
			     ->add(route('members.myprofile') . '#training', 'Training', null, [], ['id' => 'trainingTab']);
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

	/**
	 * Attach a list of user accounts
	 */
	private function attachUserList()
	{
		// Attach to each view
		View::composer([
			'committee.view',
			'pages.form',
			'events.create',
		], function ($view) {
			$users = User::active()->nameOrder()->getSelect();
			$view->with('users', $users);
		});
	}

	private function attachMemberList()
	{
		View::composer([
			'training.skills.modal.*',
		], function ($view) {
			$members = User::member()->active()->nameOrder()->getSelect();
			$view->with('members', $members);
		});
	}

	/**
	 * Attach a list of events for the given user
	 */
	private function attachMemberEvents()
	{
		View::composer('members.partials.events', function ($view) {
			$user          = $view->getData()['user'];
			$events_past   = Event::forMember($user)
			                      ->past()
			                      ->orderDesc()
			                      ->distinct()
			                      ->get();
			$events_active = Event::forMember($user)
			                      ->activeAndFuture()
			                      ->orderDesc()
			                      ->distinct()
			                      ->get();
			$view->with([
				'events_past'   => $events_past,
				'events_active' => $events_active,
			]);
		});
	}

	/**
	 * Attach a list of skills for the given user.
	 */
	private function attachMemberSkills()
	{
		View::composer([
			'members.partials.skills',
			'training.skills.index',
		], function ($view) {
			// Get the categories and uncategorised skills
			$categories    = TrainingCategory::orderBy('name', 'ASC')
			                                 ->get();
			$uncategorised = TrainingSkill::whereNull('category_id')
			                              ->orderBy('name', 'ASC')
			                              ->get();

			// Add the uncategorised
			$categories->add((object) [
				'id'     => null,
				'name'   => 'Uncategorised',
				'skills' => $uncategorised,
			]);

			// Create the list of skills
			$skills = $awardSkills = [];
			$user   = Auth::user();
			foreach($categories as $category) {
				$skills[$category->name] = $awardSkills[$category->name] = [];
				foreach($category->skills as $skill) {
					$skills[$category->name][$skill->id] = $skill->name;
					if($user->isAdmin() || ($user->hasSkill($skill) && $user->getSkill($skill)->level == 3)) {
						$awardSkills[$category->name][$skill->id] = $skill->name;
					}
				}
			}

			$view->with([
				'skillCategories' => $categories,
				'awardSkills'     => $awardSkills,
				'skillList'       => $skills,
			]);
		});
	}

	/**
	 * Attach the current user object to all views.
	 */
	private function attachActiveUser()
	{
		View::composer('*', function ($view) {
			$view->with('activeUser', Auth::user() ?: new User());
		});
	}
}