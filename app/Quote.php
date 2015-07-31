<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
	/**
	 * The attributes that are fillable by mass assignment.
	 * @var array
	 */
	protected $fillable = [
		"culprit",
		"quote",
		"date",
		"added_by",
	];

	/**
	 * Define the additional fields that should be Carbon instances.
	 * @var array
	 */
	protected $dates = [
		'date'
	];

	/**
	 * Define the creator foreign key link
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function creator()
	{
		return $this->belongsTo('\App\User', 'added_by');
	}
}
