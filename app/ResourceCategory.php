<?php

namespace App;

class ResourceCategory extends Model
{
	const FLAG_RISK_ASSESSMENT = 1;
	const FLAG_EVENT_REPORT    = 2;
	const FLAG_MEETING_AGENDA  = 3;
	const FLAG_MEETING_MINUTES = 4;

	/**
	 * Define the
	 * @var array
	 */
	public static $Flags = [
		0                          => 'None',
		self::FLAG_RISK_ASSESSMENT => 'Risk Assessment',
		self::FLAG_EVENT_REPORT    => 'Event Report',
		self::FLAG_MEETING_AGENDA  => 'Meeting Agenda',
		self::FLAG_MEETING_MINUTES => 'Meeting Minutes',
	];

	/**
	 * Define the static rules for validating categories.
	 * @var array
	 */
	protected static $ValidationRules = [
		'name' => 'required',
		'slug' => 'required|regex:/^[a-zA-Z0-9-]+$/|unique:resource_categories,slug',
	];

	/**
	 * Define the validation messages.
	 * @var array
	 */
	protected static $ValidationMessages = [
		'name.required' => 'Please enter the category name',
		'slug.required' => 'Please enter a slug',
		'slug.regex'    => 'The slug can only include letters, numbers and hyphens',
		'slug.unique'   => 'That slug is already in use',
		'flag.in'       => 'Please choose a valid type',
	];

	/**
	 * Define the attributes that are mass assignable.
	 * @var array
	 */
	public $fillable = [
		'name',
		'slug',
		'flag',
	];

	/**
	 * Set the correct table name.
	 * @var string
	 */
	public $table = 'resource_categories';

	/**
	 * Disable timestamps.
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * Define the relationship with the category's resources.
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function resources()
	{
		return $this->hasMany('App\Resource', 'category_id', 'id');
	}

	public static function getValidationRules()
	{
		static::$ValidationRules['flag'] = 'in:' . implode(',', array_keys(static::$Flags));

		return call_user_func_array('parent::getValidationRules', func_get_args());
	}

	public function setFlagAttribute($flag)
	{
		if(!isset(self::$Flags[$flag])) $flag = 0;
		$this->attributes['flag'] = $flag == 0 ? null : $flag;
	}
}
