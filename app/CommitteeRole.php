<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Szykra\Notifications\Flash;

class CommitteeRole extends Model
{
	/**
	 * Disable timestamps
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * The attributes filleable by mass-assignment.
	 * @var array
	 */
	protected $fillable = [
		'name',
		'description',
		'email',
		'user_id',
		'order',
	];

	/**
	 * Attach a listener to the 'deleted' event.
	 */
	public static function boot()
	{
		parent::boot();

		// Adjust the order of the other roles on delete
		static::deleted(function ($role) {
			$adjust_roles = static::where('order', '>', $role->order)->get();
			foreach($adjust_roles as $r) {
				$r->order--;
				$r->save();
			}
		});
	}

	/**
	 * Create the relationship with the user.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo('App\User');
	}

	/**
	 * Override setting the user_id attribute to automatically adjust user roles.
	 * @param $value
	 */
	public function setUserIdAttribute($value)
	{
		// Only process the change in permissions
		// if the assigned user has changed
		$old_id = $this->getAttributeValue('user_id');
		if($old_id != $value) {
			// Don't allow self-unassignment
			if($old_id == Auth::user()->id) {
				Flash::warning('You can\'t remove yourself from the committee');

				return;
			}

			// Get the necessary roles
			$committee = Role::where('name', 'committee')->first();
			$member    = Role::where('name', 'member')->first();

			// Look through the database for any other committee roles for
			// the old user. If they exist then we don't want to remove
			// their committee permissions.
			$old_user = User::find($old_id);
			if($old_user && $old_user->hasRole($committee->name)) {
				if(CommitteeRole::where('user_id', '=', $old_user->id)->where('id', '<>', $this->id)->get()->count() == 0) {
					$old_user->detachRole($committee);
					$old_user->attachRole($member);
				}
			}

			// Always give the new user committee permissions
			$new_user = User::find($value);
			if($new_user && !$new_user->hasRole($committee->name)) {
				$new_user->attachRole($committee);
				$new_user->detachRole($member);
			}
		}

		// Set the new id
		$this->attributes['user_id'] = $value;
	}

	public function setOrderAttribute($newOrder)
	{
		// Get the current order
		$currentOrder = $this->getAttribute('order');

		// If the current order attribute is null this is an addition
		// so just increment the order for the following roles.
		if($currentOrder === null) {
			DB::table('committee_roles')->where('order', '>=', $newOrder)->increment('order');
		}
		// If they're different then process the change
		// in order by moving the roles in between.
		else if($currentOrder != $newOrder) {
			// If the role has been moved 'later' then decrement the
			// order for the roles in between the two positions
			if($newOrder > $currentOrder) {
				$newOrder--;
				DB::table('committee_roles')->where('order', '>', $currentOrder)->where('order', '<=', $newOrder)->decrement('order');
			}
			// If the role has been moved 'earlier' then increment the
			// order for the roles in between the two positions
			else {
				DB::table('committee_roles')->where('order', '<', $currentOrder)->where('order', '>=', $newOrder)->increment('order');
			}
		}

		// Set the value
		$this->attributes['order'] = $newOrder;
	}
}
