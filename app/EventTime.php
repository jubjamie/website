<?php

namespace App;

class EventTime extends Model
{
	/**
	 * The attributes fillable by mass assignment.
	 * @var array
	 */
	protected $fillable = [
		'event_id',
		'name',
		'start',
		'end',
	];

	/**
	 * Define the additional fields that should be Carbon instances.
	 * @var array
	 */
	protected $dates = [
		'start',
		'end',
	];

	/**
	 * Define the foreign key relationship with the event.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function event()
	{
		return $this->belongsTo('App\Event');
	}
}
