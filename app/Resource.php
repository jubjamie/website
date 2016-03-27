<?php

namespace App;

use Illuminate\Support\Facades\Auth;

class Resource extends Model
{
	const TYPE_FILE = 1;
	const TYPE_GDOC = 2;

	/**
	 * Define the resource types.
	 * @var array
	 */
	const TYPES = [
		self::TYPE_FILE => 'Uploaded File',
		//self::TYPE_GDOC => 'Google Drive',
	];

	/**
	 * Define the attributes that are mass assignable.
	 * @var array
	 */
	public $fillable = [
		'title',
		'description',
		'category_id',
		'event_id',
		'author_id',
		'type',
		'href',
		'access_id',
	];

	/**
	 * A method to easily get the directory in which all resource files are stored.
	 * @return string
	 */
	public static function getParentDirectory()
	{
		return base_path('resources/resources');
	}

	/**
	 * Get an associate of the access levels.
	 * @param bool $includePublic
	 * @return array
	 */
	public static function getAccessList($includePublic = true)
	{
		$access = $includePublic ? ['' => 'Everyone (Public)'] : [];

		return $access + Permission::where('name', 'LIKE', 'resources.%')->orderBy('display_name', 'ASC')->lists('display_name', 'id')->all();
	}

	/**
	 * Define the relationship with the resource's category.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function category()
	{
		return $this->belongsTo('App\ResourceCategory', 'category_id');
	}

	/**
	 * Define the relationship with the resource's tags.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function tags()
	{
		return $this->belongsToMany('App\ResourceTag', 'resource_tag')->orderBy('resource_tags.name', 'ASC');
	}

	/**
	 * Define the relationship with the resource's event.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function event()
	{
		return $this->belongsTo('App\Event', 'event_id', 'id');
	}

	/**
	 * Define the relationship with the resource's author.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function author()
	{
		return $this->belongsTo('App\User', 'author_id', 'id');
	}

	/**
	 * Define the relationship with the access.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function access()
	{
		return $this->belongsTo('App\Permission', 'access_id', 'id');
	}

	/**
	 * Scope to perform a FULLTEXT search on the title and description.
	 * This automatically encloses words in wildcards to allow substrings to match.
	 * @param $query
	 * @param $searchTerm
	 * @return mixed
	 */
	public function scopeSearch($query, $searchTerm)
	{
		return $query->whereRaw("MATCH(title,description) AGAINST(? IN BOOLEAN MODE)", [
			preg_replace('/([\s]{2,})/', ' ', preg_replace('/(^|\s)([\w]+)(\s|$)/', '$1*$2*$3', preg_replace('/\s/', '  ', $searchTerm))),
		]);
	}

	/**
	 * Scope to specify a tag the resource(s) should have.
	 * @param $query
	 * @param $tags
	 * @return mixed
	 */
	public function scopeWithTags($query, $tags)
	{
		return $query->leftJoin('resource_tag', 'resources.id', '=', 'resource_tag.resource_id')
		             ->leftJoin('resource_tags', 'resource_tag.resource_tag_id', '=', 'resource_tags.id')
		             ->whereIn('resource_tags.slug', $tags)
		             ->groupBy('resources.id')
		             ->havingRaw('COUNT(DISTINCT resource_tags.slug) = ' . count($tags));
	}

	/**
	 * Scope to specify the category the resource(s) should be in.
	 * @param $query
	 * @param $categorySlug
	 * @return mixed
	 */
	public function scopeInCategory($query, $categorySlug)
	{
		return $query->leftJoin('resource_categories', 'resources.category_id', '=', 'resource_categories.id')
		             ->whereNested(function ($query) use ($categorySlug) {
			             $query->where('resource_categories.slug', $categorySlug);
		             });
	}

	/**
	 * Scope to only get resources the active user can access.
	 * @param $query
	 * @return mixed
	 */
	public function scopeAccessible($query)
	{
		if(Auth::check()) {
			return $query->leftJoin('permission_role', 'resources.access_id', '=', 'permission_role.permission_id')
			             ->leftJoin('role_user', 'permission_role.role_id', '=', 'role_user.role_id')
			             ->leftJoin('users', 'role_user.user_id', '=', 'users.id')
			             ->whereNested(function ($query) {
				             $query->where('users.id', Auth::user()->id)
				                   ->orWhereNull('access_id');
			             });

		} else {
			return $query->whereNull('access_id');
		}
	}

	/**
	 * Test if the resource is attached to an event.
	 * @return bool
	 */
	public function isAttachedToEvent()
	{
		return $this->event_id !== null;
	}

	/**
	 * Test if the resource is categorised.
	 * @return bool
	 */
	public function isCategorised()
	{
		return $this->category_id !== null;
	}

	/**
	 * Test if the resource is a file.
	 * @return bool
	 */
	public function isFile()
	{
		return $this->type == self::TYPE_FILE;
	}

	/**
	 * Test if the resource is a Google Doc.
	 * @return bool
	 */
	public function isGDoc()
	{
		return $this->type == self::TYPE_GDOC;
	}

	/**
	 * Test if the resource is flagged as a risk assessment.
	 * @return bool
	 */
	public function isRiskAssessment()
	{
		return $this->isCategorised() && $this->category->flag === ResourceCategory::FLAG_RISK_ASSESSMENT;
	}

	/**
	 * Test if the resource is flagged as an event report.
	 * @return bool
	 */
	public function isEventReport()
	{
		return $this->isCategorised() && $this->category->flag === ResourceCategory::FLAG_EVENT_REPORT;
	}

	/**
	 * Test if the resource is flagged as a meeting agenda.
	 * @return bool
	 */
	public function isMeetingAgenda()
	{
		return $this->isCategorised() && $this->category->flag === ResourceCategory::FLAG_MEETING_AGENDA;
	}

	/**
	 * Test if the resource is flagged as meeting minutes.
	 * @return bool
	 */
	public function isMeetingMinutes()
	{
		return $this->isCategorised() && $this->category->flag === ResourceCategory::FLAG_MEETING_MINUTES;
	}

	/**
	 * Test is a user can view the resource.
	 * @param \App\User $user
	 * @return bool
	 */
	public function canAccess(User $user)
	{
		return $this->access_id === null ? true : $user->can($this->access->name);
	}

	/**
	 * Get all the resource's tags as an array of IDs.
	 * @return mixed
	 */
	public function getTagsAttribute()
	{
		return $this->tags()->lists('id')->toArray();
	}

	/**
	 * Define a shortcut for getting the name of the resource's category.
	 * @return string
	 */
	public function getCategoryNameAttribute()
	{
		return $this->isCategorised() ? $this->category->name : 'Uncategorised';
	}

	/**
	 * Define a shortcut for getting the title of the resource's access.
	 * @return string
	 */
	public function getAccessNameAttribute()
	{
		return $this->access === null ? 'Everyone' : $this->access->display_name;
	}

	/**
	 * Get the file's extension. As PDFs are the only file type
	 * currently supported there is no logic here; however it
	 * does provide flexibility for the future.
	 * @return string
	 */
	public function getFileExtension()
	{
		return $this->isFile() ? 'pdf' : '';
	}

	/**
	 * Get the name of the file.
	 * @return string
	 */
	public function getFileName()
	{
		if($this->isFile()) {
			return ($this->id . '.' . $this->getFileExtension());
		} else if($this->isGDoc()) {
			return $this->href;
		} else {
			return '';
		}
	}

	/**
	 * Get the full path of the file.
	 * @return string
	 */
	public function getFilePath()
	{
		if($this->isFile()) {
			return static::getParentDirectory() . '/' . $this->getFileName();
		} else if($this->isGDoc()) {
			return 'https://drive.google.com/open?id=' . $this->href;
		} else {
			return '';
		}
	}

	/**
	 * Get an associative array of headers to send
	 * when streaming or downloadin the resource.
	 * @return array
	 */
	public function getHeaders()
	{
		if($this->isFile()) {
			return [
				'Content-Type'        => 'application/pdf',
				'Content-Disposition' => 'inline; filename="' . ($this->title . '.' . $this->getFileExtension()) . '"',
				'Content-Length'      => filesize($this->getFilePath()),
			];
		} else {
			return [];
		}
	}
}
