<?php

namespace App;

class TrainingSkill extends Model
{
	/**
	 * Define the names of the skill levels.
	 * @var array
	 */
	public static $LevelNames = [
		1 => 'Level 1',
		2 => 'Level 2',
		3 => 'Level 3',
	];

	/**
	 * Define the validation rules.
	 * @var array
	 */
	protected static $ValidationRules = [
		'name'                => 'required',
		'category_id'         => 'exists:training_categories,id',
		'description'         => 'required',
		'requirements_level1' => 'required',
		'requirements_level2' => 'required',
		'requirements_level3' => 'required',
	];

	/**
	 * Define the validation messages.
	 * @var array
	 */
	protected static $ValidationMessages = [
		'name.required'                => 'Please enter the skill name',
		'category_id.exists'           => 'Please choose a valid category',
		'description.required'         => 'Please enter a brief description of the skill',
		'requirements_level1.required' => 'Please enter the requirements for Level 1',
		'requirements_level2.required' => 'Please enter the requirements for Level 2',
		'requirements_level3.required' => 'Please enter the requirements for Level 3',
	];

	/**
	 * The attributes fillable by mass assignment.
	 * @var array
	 */
	protected $fillable = [
		'name',
		'category_id',
		'description',
		'requirements_level1',
		'requirements_level2',
		'requirements_level3',
	];

	/**
	 * Get a proficiency level as a nicely formatted HTML string.
	 * @param $level
	 * @return string
	 */
	public static function getProficiencyHtml($level)
	{
		// Check the level
		$level = (int) $level;
		if($level < 1 || $level > 3) {
			return '';
		}

		// Build the HTML
		$html = '<span class="skill-proficiency" title="' . static::$LevelNames[$level] . '">';
		for($i = 1; $i <= $level; $i++) {
			$html .= '<span class="fa fa-star"></span>';
		}
		for(; $i <= 3; $i++) {
			$html .= '<span class="fa fa-star-o"></span>';
		}
		$html .= '</span>';

		return $html;
	}

	/**
	 * Define the relationship to the category.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function category()
	{
		return $this->belongsTo('App\TrainingCategory', 'category_id');
	}

	/**
	 * Get the list of users with this skill.
	 * @return mixed
	 */
	public function getUsersAttribute()
	{
		// Get all the users
		$users = User::select('users.*', 'training_awarded_skills.level')
		             ->join('training_awarded_skills', 'users.id', '=', 'training_awarded_skills.user_id')
		             ->where('training_awarded_skills.skill_id', $this->id)
		             ->nameOrder()
		             ->get();

		// Sort the users by level
		$levels = [1 => [], 2 => [], 3 => []];
		foreach($users as $user) {
			$levels[$user->level][] = $user;
		}

		return $levels;
	}
}
