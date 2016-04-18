<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Szykra\Notifications\Flash;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
	use Authenticatable, CanResetPassword, EntrustUserTrait;

	/**
	 * User account type constants
	 */
	const MEMBER    = 1;
	const COMMITTEE = 2;
	const ASSOCIATE = 3;
	const SU        = 4;
	const ADMIN     = 5;

	/**
	 * Define the list of user account types for the create form.
	 * @var array
	 */
	public static $CreateAccountTypes = [
		self::MEMBER    => 'Standard Member',
		self::COMMITTEE => 'Committee',
		self::ASSOCIATE => 'Associate',
		self::SU        => 'SU Officer',
	];

	/**
	 * Define the list of user account types for the edit form.
	 * @var array
	 */
	public static $EditAccountTypes = [
		self::MEMBER    => 'Standard Member',
		self::COMMITTEE => 'Committee',
		self::ASSOCIATE => 'Associate',
		self::SU        => 'SU Officer',
		self::ADMIN     => 'Web Admin (not committee)',
	];

	/**
	 * Define the validation rules.
	 * @var array
	 */
	protected static $ValidationRules = [
		'name'         => 'required|name',
		'nickname'     => 'regex:/[a-zA-Z]+/',
		'username'     => 'required|regex:/[a-zA-Z0-9_\.]+/|unique:users,username',
		'email'        => 'required|email|unique:users,email',
		'phone'        => 'phone',
		'dob'          => 'date_format:d/m/Y|regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/',
		'show_email'   => 'boolean',
		'show_phone'   => 'boolean',
		'show_address' => 'boolean',
		'show_age'     => 'boolean',
	];

	/**
	 * Define the validation messages.
	 * @var array
	 */
	protected static $ValidationMessages = [
		'name.required'     => 'Please enter your name',
		'name.name'         => 'Please enter your forename and surname',
		'nickname.regex'    => 'Please just use letters',
		'username.required' => 'Please enter their BUCS username',
		'username.regex'    => 'Please use only letters and numbers',
		'username.unique'   => 'A user with that username already exists',
		'email.required'    => 'Please enter your email address',
		'email.email'       => 'Please enter a valid email address',
		'email.unique'      => 'That email address is already in use by another user',
		'phone.phone'       => 'Please enter a valid phone number',
		'dob.date_format'   => 'Please enter your DOB in the format dd/mm/YYYY',
		'dob.regex'         => 'Please enter your DOB in the format dd/mm/YYYY',
	];

	/**
	 * The database table used by the model.
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 * Some of these are "pseudo-attributes" as they don't exist
	 * in the database but are used to add functionality
	 * @var array
	 */
	protected $fillable = [
		'username',
		'forename',
		'surname',
		'name', // Pseudo
		'nickname',
		'email',
		'password',
		'status',
		'phone',
		'address',
		'tool_colours',
		'dob',
		'show_email',
		'show_phone',
		'show_address',
		'show_age',
		'type', // Pseudo
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * Define the attributes which should be provided as Carbon dates.
	 * @var array
	 */
	protected $dates = [
		'dob',
	];

	protected $casts = [
		'show_email'   => 'boolean',
		'show_phone'   => 'boolean',
		'show_address' => 'boolean',
		'show_age'     => 'boolean',
	];

	/**
	 * Override the default ::create method to automatically assign some attributes.
	 * This also automatically sets up the role and sends the new user an email.
	 * @param array $attributes
	 * @param bool  $sendEmail
	 * @return static
	 */
	public static function create(array $attributes = [], $sendEmail = true)
	{
		// Set up the default parameters
		if(!isset($attributes['email']) && isset($attributes['username'])) {
			$attributes['email'] = $attributes['username'] . '@bath.ac.uk';
		}
		if(!isset($attributes['password'])) {
			$password               = str_random(15);
			$attributes['password'] = bcrypt($password);
		}
		if(!isset($attributes['status'])) {
			$attributes['status'] = true;
		}
		if(isset($attributes['type'])) {
			$type = $attributes['type'];
			unset($attributes['type']);
		}

		// Create the user
		$user = parent::create($attributes);
		if(isset($type)) {
			$user->type = $type;
		}

		if($user && $sendEmail && isset($password)) {
			// Send an email to the new user
			Mail::queue('emails.users.create', [
				'name'     => $user->forename,
				'password' => $password,
			], function ($message) use ($user) {
				$message->subject('Your new Backstage account')
				        ->to($user->email, $user->name);
			});
		}

		return $user;
	}

	/**
	 * Override the validation rules to exclude
	 * the current user from the unique checks
	 * @return array
	 */
	public static function getValidationRules()
	{
		static::$ValidationRules['email'] =
			'required|email|unique:users,email,' . (Route::currentRouteName() == 'members.myprofile.do' ? Auth::user()->id : '');

		return call_user_func_array('parent::getValidationRules', func_get_args());
	}

	/**
	 * Define the pages foreign key link.
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function pages()
	{
		return $this->hasMany('App\Page');
	}

	/**
	 * Define the quotes foreign key link
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function quotes()
	{
		return $this->hasMany('App\Quote', 'added_by');
	}

	/**
	 * Define the relationship to the user's awarded skills.
	 * @return $this
	 */
	public function skills()
	{
		return $this->hasMany('App\TrainingAwardedSkill');
	}

	/**
	 * Add a scope for only getting active accounts.
	 * @param $query
	 */
	public function scopeActive($query)
	{
		$query->where('status', true);
	}

	/**
	 * Add a scope for only getting BTS member accounts.
	 * @param $query
	 */
	public function scopeMember($query)
	{
		$query->select('users.*')
		      ->join('role_user', 'users.id', '=', 'role_user.user_id')
		      ->join('roles', 'role_user.role_id', '=', 'roles.id')
		      ->whereIn('roles.name', ['member', 'committee', 'associate']);
	}

	/**
	 * Add a scope for getting users that are signed up to an event.
	 * @param            $query
	 * @param \App\Event $event
	 */
	public function scopeCrewingEvent($query, Event $event)
	{
		$query->select('users.*')
		      ->join('event_crew', 'users.id', '=', 'event_crew.user_id')
		      ->where('event_crew.event_id', $event->id);
	}

	/**
	 * Add a scope for getting users which are not signed up to an event.
	 * @param            $query
	 * @param \App\Event $event
	 */
	public function scopeNotCrewingEvent($query, Event $event)
	{
		$query->select('users.*')
		      ->whereNotIn('users.id', self::crewingEvent($event)->lists('id'))
		      ->whereNotIn('users.id', self::select('users.*')
		                                   ->join('events', 'users.id', '=', 'events.em_id')
		                                   ->where('events.id', $event->id)
		                                   ->lists('id'));
	}

	/**
	 * Add a scope to order the users by their name.
	 * @param $query
	 */
	public function scopeNameOrder($query)
	{
		$query->orderBy('surname', 'ASC')
		      ->orderBy('forename', 'ASC');
	}

	/**
	 * Add a scope to get the results and produce an array suitable for <select> elements.
	 * @param $query
	 * @return array
	 */
	public function scopeGetSelect($query)
	{
		$results           = $query->get();
		$results_formatted = [];
		foreach($results as $result) {
			$results_formatted[$result->id] = sprintf("%s (%s)", $result->name, $result->username);
		}

		return $results_formatted;
	}

	public function scopeSearch($query, $term)
	{
		if(stripos($term, ' ')) {
			$query->where(function($query) use($term) {
				$query->where('forename', 'LIKE', '%' . substr($term, 0, stripos($term, ' ')) . '%')
				      ->where('surname', 'LIKE', '%' . substr($term, stripos($term, ' ') + 1) . '%')
				      ->orWhere('nickname', 'LIKE', '%' . $term . '%');
			});
		} else {
			$query->where(function ($query) use ($term) {
				$query->where('username', 'LIKE', '%' . $term . '%')
				      ->orWhere('nickname', 'LIKE', '%' . $term . '%')
				      ->orWhere('forename', 'LIKE', '%' . $term . '%')
				      ->orWhere('surname', 'LIKE', '%' . $term . '%')
				      ->orWhere('email', 'LIKE', '%' . $term . '%');
			});
		}
	}

	/**
	 * Get the list of rules for validating the profile save inputs.
	 * @return array
	 */
	public function getProfileValidationRules()
	{
		return [
			'name'         => 'required|name',
			'username'     => 'required|regex:/[a-zA-Z0-9_]+/|unique:users,username,' . $this->id,
			'email'        => 'required|email|unique:users,email,' . $this->id,
			'phone'        => 'phone',
			'dob'          => 'date_format:Y-m-d|regex:/[0-9]{4}-[0-9]{2}-[0-9]{2}/',
			'show_email'   => 'boolean',
			'show_phone'   => 'boolean',
			'show_address' => 'boolean',
			'show_age'     => 'boolean',
		];
	}

	/**
	 * Get the list of error messages for the profile save validation.
	 * @return array
	 */
	public function getProfileValidationMessages()
	{
		return [
			'name.required'     => 'Please enter the user\'s name',
			'name.name'         => 'Please enter their forename and surname',
			'username.required' => 'Please enter their BUCS username',
			'username.regex'    => 'Please use only letters and numbers',
			'username.unique'   => 'A user with that username already exists',
			'email.required'    => 'Please enter the user\'s email address',
			'email.email'       => 'Please enter a valid email address',
			'email.unique'      => 'That email address is already in use by another user',
			'phone.phone'       => 'Please enter a valid phone number',
			'dob.date_format'   => 'Please enter their DOB in the format YYYY-mm-dd',
			'dob.regex'         => 'Please enter their DOB in the format YYYY-mm-dd',
		];
	}

	/**
	 * Test if the user is a BTS member.
	 * @return bool
	 */
	public function isMember()
	{
		return $this->hasRole(['member', 'committee', 'associate'], false);
	}

	/**
	 * Test if the user is a committee member.
	 * @return bool
	 */
	public function isCommittee()
	{
		return $this->hasRole('committee');
	}

	/**
	 * Test if the user is an associate.
	 * @return bool
	 */
	public function isAssociate()
	{
		return $this->hasRole('associate');
	}

	/**
	 * Test if the user is an SU officer.
	 * @return bool
	 */
	public function isSU()
	{
		return $this->hasRole('su');
	}

	/**
	 * Test if the user is an admin.
	 * @return bool
	 */
	public function isAdmin()
	{
		return $this->isCommittee() || $this->hasRole('super_admin');
	}

	/**
	 * Get the user's full name.
	 * @return string
	 */
	public function getNameAttribute()
	{
		return $this->forename . ' ' . $this->surname;
	}

	/**
	 * Allow setting the forename and surname as a "name".
	 * @param $value
	 * @return $this
	 */
	public function setNameAttribute($value)
	{
		list($this->forename, $this->surname) = explode(' ', $value);

		return $this;
	}

	/**
	 * Get the user's forename.
	 * @return string
	 */
	public function getForenameAttribute()
	{
		return ucfirst($this->attributes['forename']);
	}

	/**
	 * Get the user's surname.
	 * @return string
	 */
	public function getSurnameAttribute()
	{
		return ucfirst($this->attributes['surname']);
	}

	/**
	 * Get the user's role as an "account type".
	 * @return int
	 */
	public function getTypeAttribute()
	{
		if($this->isAdmin() && !$this->isMember()) {
			return self::ADMIN;
		} else if($this->isSU()) {
			return self::SU;
		} else if($this->isAssociate()) {
			return self::ASSOCIATE;
		} else if($this->isCommittee()) {
			return self::COMMITTEE;
		} else {
			return self::MEMBER;
		}
	}

	/**
	 * Set the user's role from an "account type".
	 * @param $value
	 */
	public function setTypeAttribute($value)
	{
		if($value == self::ADMIN) {
			$this->roles()->sync([Role::where('name', 'super_admin')->first()->id]);
		} else if($value == self::SU) {
			$this->roles()->sync([Role::where('name', 'su')->first()->id]);
		} else if($value == self::ASSOCIATE) {
			$this->roles()->sync([Role::where('name', 'associate')->first()->id]);
		} else if($value == self::COMMITTEE) {
			$this->roles()->sync([Role::where('name', 'committee')->first()->id]);
		} else {
			$this->roles()->sync([Role::where('name', 'member')->first()->id]);
		}
	}

	/**
	 * Make the user account archived
	 * @return bool
	 */
	public function archive()
	{
		// Check the selected user isn't the current user
		if(Auth::check() && $this->id == Auth::user()->id) {
			Flash::warning('You cannot archive your own account');

			return false;
		}

		// Change status
		return $this->update(['status' => false]);
	}

	/**
	 * Make the user a committee member.
	 * @return bool
	 */
	public function makeCommittee()
	{
		if($this->id == Auth::user()->id) {
			Flash::warning('You cannot make yourself a committee member');

			return false;
		}


		$this->roles()->sync([Role::where('name', 'committee')->first()->id]);

		return true;
	}

	/**
	 * Make the user an associate
	 * @return bool
	 */
	public function makeAssociate()
	{
		if($this->id == Auth::user()->id) {
			Flash::warning('You cannot make yourself an associate');

			return false;
		}

		$this->roles()->sync([Role::where('name', 'associate')->first()->id]);

		return true;
	}

	/**
	 * @param string $after
	 * @return string
	 */
	public function getPossessiveName($after = '')
	{
		return $this->name . "'" . (substr($this->name, -1) == 's' ? '' : 's') . ' ' . $after;
	}

	/**
	 * Change the user's avatar and resize to 500x500.
	 * @param UploadedFile $image
	 * @return $this
	 */
	public function setAvatar(UploadedFile $image)
	{
		// Convert, resize and save
		Image::make($image)
		     ->fit(500, 500)
		     ->save($this->getAvatarPath(true));

		return $this;
	}

	/**
	 * Get the URL of the user's profile picture to be used in img tags.
	 * @return string
	 */
	public function getAvatarUrl()
	{
		return $this->getAvatarPath(false, true);
	}

	/**
	 * Check if the user has a custom avatar image.
	 * @return bool
	 */
	public function hasAvatar()
	{
		return file_exists($this->getAvatarPath(true, false));
	}

	/**
	 * Get the URL or path of the user's avatar.
	 * @param bool $absolute
	 * @param bool $checkExists
	 * @return string
	 */
	public function getAvatarPath($absolute = false, $checkExists = false)
	{
		$basePath = '/images/profiles/';
		$imgPath  = $basePath . $this->username . '.jpg';
		$path     = !$checkExists || $this->hasAvatar() ? $imgPath : ($basePath . 'blank.jpg');

		return ($absolute ? base_path('public') : '') . $path;

	}

	/**
	 * @return mixed
	 */
	public function getToolColours()
	{
		// Initialise
		$toolColours = strtolower($this->tool_colours);
		$toolColours = str_replace(['and', ';', '&', ','], ' ', $toolColours);
		$toolColours = trim(preg_replace("/\s+/", ' ', $toolColours));
		$recognised  = ["red", "blue", "green", "yellow", "white", "black", "brown", "pink", "purple", "grey", "orange", "earth", "rainbow", "turquoise"];

		if(!empty($toolColours)) {
			// Look for initials
			$initials = null;
			if(preg_match("/(?:with)?\s*initials(\s*\(?([a-z0-9]+)\)?)?/i", $toolColours, $matches)) {
				$initials    = isset($matches[2]) ? $matches[2] : (substr($this->forename, 0, 1) . substr($this->surname, 0, 1));
				$toolColours = preg_replace("/with initials(\s*\(?[a-z0-9]+\)?)?/i", '', $toolColours);
			}

			// Look for colour entries
			$tool_colours = [];
			$title = '';
			preg_match_all("/(light|fluorescent)?\s*([a-z]+)(\s*\([a-z+\s]+\))?/i", $toolColours, $matches);
			foreach($matches[0] as $i => $full_colour) {
				if(!in_array($matches[2][$i], $recognised)) {
					return $this->tool_colours;
				}

				// Add support for striped colours
				$shape = 'wrench';
				$colours = [$matches[2][$i]];
				switch($matches[2][$i]) {
					case 'earth':
						$colours = ['yellow', 'green'];
						break;
					case 'rainbow':
						$colours = ['red', 'orange', 'yellow', 'green', 'blue', 'purple'];
						break;
					default:
				}

				// Build the html entry
				$title .= ($matches[1][$i] ? (ucfirst($matches[1][$i] . ' ')) : '') . ucfirst($matches[2][$i]) . ', ';
				$tool_colours[$i] = '<span class="tool-colour' . (count($colours) > 1 ? ' striped' : '') . '">';
				for($j = count($colours) - 1; $j >= 0; $j--) {
					$tool_colours[$i] .= '<span class="fa fa-' . $shape . ' ' . trim($colours[$j] . ' ' . ($matches[1][$i] ?: '')) . '"></span>';
				}
				$tool_colours[$i] .= '</span>';
			}

			// End
			$tool_html = '<span class="tool-colours" title="' . trim(rtrim($title, ', ') . ' ' . ($initials ? ((!empty($title) ? 'with ' : '') . 'initials') : '')) . '">' . implode('', $tool_colours);
			if($initials) $tool_html .= '<span class="initials">(' . trim($initials) . ')</span>';
			$tool_html .= '</span>';

			return $tool_html;
		} else {
			return '';
		}
	}

	/**
	 * Test if the user has a certain skill assigned.
	 * @param \App\TrainingSkill $skill
	 * @return bool
	 */
	public function hasSkill(TrainingSkill $skill)
	{
		return $this->skills
			       ->where('skill_id', $skill->id)
			       ->count() == 1;
	}

	/**
	 * Get the details of an awarded skill.
	 * @param \App\TrainingSkill $skill
	 * @return mixed
	 */
	public function getSkill(TrainingSkill $skill)
	{
		return $this->skills->where('skill_id', $skill->id)->first();
	}

	/**
	 * Count the number of skills the user has, with the ability to specify the category.
	 * Use an ID of -1 to specify 'uncategorised'
	 * @param null $categoryId
	 * @return int
	 */
	public function countSkills($categoryId = null)
	{
		if(!$categoryId) {
			return count($this->skills);
		} else {
			$count = 0;
			foreach($this->skills as $skill) {
				if(($skill->skill->category_id == $categoryId) || ($categoryId == -1 && !$skill->skill->category_id)) {
					$count++;
				}
			}

			return $count;
		}
	}

	/**
	 * Set the level of a skill
	 * @param $skillId
	 * @param $level
	 */
	public function setSkillLevel($skillId, $level)
	{
		// Create array of attributes to set
		$data = [
			'skill_id'   => $skillId,
			'level'      => $level,
			'awarded_by' => Auth::user()->id,
		];

		// Update or create
		if($this->skills->where('skill_id', $skillId)->count() == 1) {
			$this->skills->where('skill_id', $skillId)->first()->update($data);
		} else {
			$this->skills()->create($data);
		}
	}

	/**
	 * Check if the user has a proposal for an event pending.
	 * @param \App\TrainingSkill $skill
	 * @return bool
	 */
	public function hasProposalPending(TrainingSkill $skill)
	{
		return TrainingSkillProposal::notAwarded()
		                            ->where('user_id', $this->id)
		                            ->where('skill_id', $skill->id)
		                            ->count() > 0;
	}
}
