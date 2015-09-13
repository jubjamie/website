<?php

namespace App;

class TrainingCategory extends Model
{
	/**
	 * The database table name.
	 * @var string
	 */
    protected $table = 'training_categories';

	/**
	 * The attributes fillable by mass assignment.
	 * @var array
	 */
	protected $fillable = [
		'name'
	];

	public static function selectList()
	{
		return [null => '-- Uncategorised --'] + self::orderBy('name', 'ASC')->lists('name', 'id')->toArray();
	}

	/**
	 * Define the relationship with the skills.
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function skills()
	{
		return $this->hasMany('App\TrainingSkill', 'category_id')->orderBy('name', 'ASC');
	}
}
