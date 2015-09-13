<?php

namespace App;

class EventEmail extends Model
{
	/**
	 * The attributes fillable by mass assignment.
	 * @var array
	 */
	protected $fillable = [
		'event_id',
		'sender_id',
		'header',
		'body',
	];

	/**
	 * Define the foreign key relationship with the event.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function event()
	{
		return $this->belongsTo('App\Event');
	}

	/**
	 * Define the foreign key relationship with the user.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo('App\User', 'sender_id');
	}
}
