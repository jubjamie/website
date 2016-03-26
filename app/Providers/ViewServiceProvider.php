<?php

namespace App\Providers;

use App\Event;
use App\Resource;
use App\ResourceCategory;
use App\ResourceTag;
use App\TrainingCategory;
use App\TrainingSkill;
use App\User;
use Illuminate\Support\Facades\Auth;
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
		$this->composeFlash();
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
	 * Attach a list of active users.
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
			$view->with('users', ['' => '-- Select --'] + $users);
		});
	}

	/**
	 * Attach a list of active members.
	 */
	private function attachMemberList()
	{
		View::composer([
			'training.skills.modal.*',
			'elections.view',
		], function ($view) {
			$members = User::member()->active()->nameOrder()->getSelect();
			$view->with('members', ['' => '-- Select --'] + $members);
		});
	}

	/**
	 * Attach a list of events for the given user.
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
			                      ->orderBy('event_times.start', 'DESC')
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