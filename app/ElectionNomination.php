<?php

namespace App;

class ElectionNomination extends Model
{
	/**
	 * Define the database table name.
	 * @var string
	 */
	protected $table = 'election_nominations';

	/**
	 * Disable timestamps.
	 * @var bool
	 */
	public $timestamps = false;

	public $fillable = [
		'user_id',
		'position',
		'elected',
	];

	/**
	 * Define the relationship with the election.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function election()
	{
		return $this->belongsTo('App\Election', 'election_id', 'id');
	}

	public function user()
	{
		return $this->belongsTo('App\User', 'user_id', 'id');
	}

	/**
	 * Get the URL of a nominee's manifesto
	 * @return string
	 */
	public function getManifestoPath()
	{
		return $this->election->getManifestoPath() . '/' . $this->getManifestoName();
	}

	/**
	 *
	 */
	public function getManifestoName()
	{
		return $this->user->username . '_' . $this->election->getPositionSlug($this->position) . '.pdf';
	}
}
