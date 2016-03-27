<?php

namespace App;

class ResourceTag extends Model
{
	/**
	 * Define the static rules for validating tags.
	 * @var array
	 */
	protected static $ValidationRules = [
		'name' => 'required',
		'slug' => 'required|regex:/^[a-zA-Z0-9-]+$/|unique:resource_tags,slug',
	];

	/**
	 * Define the validation messages.
	 * @var array
	 */
	protected static $ValidationMessages = [
		'name.required' => 'Please enter the tag name',
		'slug.required' => 'Please enter a slug',
		'slug.regex'    => 'The slug can only include letters, numbers and hyphens',
		'slug.unique'   => 'That slug is already in use',
	];

	/**
	 * Define the attributes that are mass assignable.
	 * @var array
	 */
	public $fillable = [
		'name',
		'slug',
	];

	/**
	 * Disable timestamps.
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * Define the relationship through the pivot table.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function resources()
	{
		return $this->belongsToMany('App\Resource', 'resource_tag', 'resource_tag_id', 'resource_id');
	}
}
