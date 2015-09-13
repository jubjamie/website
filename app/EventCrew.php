<?php

namespace App;

class EventCrew extends Model
{
	/**
	 * Disable timestamps
	 * @var bool
	 */
	public $timestamps = false;
	/**
	 * The database table used by the model.
	 * @var string
	 */
	protected $table = 'event_crew';
	/**
	 * The attributes fillable by mass assignment.
	 * @var array
	 */
	protected $fillable = [
		'event_id',
		'user_id',
		'name',
		'em',
		'confirmed',
	];

	/**
	 * Define the attributes that need type-casting.
	 * @var array
	 */
	protected $casts = [
		'em'        => 'boolean',
		'confirmed' => 'boolean',
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
		return $this->belongsTo('App\User');
	}
}
