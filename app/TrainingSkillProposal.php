<?php

namespace App;

class TrainingSkillProposal extends Model
{
	/**
	 * Disable the created/updated timestamps.
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * The attributes fillable by mass assignment.
	 * @var array
	 */
	protected $fillable = [
		'skill_id',
		'user_id',
		'proposed_level',
		'reasoning',
		'date',
		'awarded_level',
		'awarded_by',
		'awarded_comment',
		'awarded_date',
	];

	/**
	 * The attributes that should be Carbon instances.
	 * @var array
	 */
	protected $dates = [
		'date',
		'awarded_date'
	];

	/**
	 * Define the relationship with the skill.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function skill()
	{
		return $this->belongsTo('App\Skill', 'skill_id');
	}

	/**
	 * Define the relationship with the user who made the proposal.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo('App\User');
	}

	/**
	 * Define the relationship with the user who awarded the proposal.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function awarder()
	{
		return $this->belongsTo('App\User', 'awarded_by');
	}
}
