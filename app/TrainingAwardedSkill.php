<?php

namespace App;

class TrainingAwardedSkill extends Model
{
	/**
	 * The attributes fillable by mass assignment.
	 * @var array
	 */
	protected $fillable = [
		'skill_id',
		'user_id',
		'level',
		'awarded_by',
	];

	/**
	 * Define the relationship with the skill's details.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function skill()
	{
		return $this->belongsTo('App\TrainingSkill', 'skill_id');
	}

	/**
	 * Define the relationship with the user the skill is awarded to.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo('App\User');
	}

	/**
	 * Define the relationship with the user who awarded the skill.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function awarder()
	{
		return $this->belongsTo('App\User', 'awarded_by');
	}

	/**
	 * Get the category using the related skill.
	 * @return \App\TrainingCategory
	 */
	public function getCategoryAttribute()
	{
		return $this->skill->category;
	}
}
