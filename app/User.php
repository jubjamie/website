<?php
    
    namespace App;
    
    use App\Notifications\ResetPassword;
    use Illuminate\Notifications\Notifiable;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Support\Facades\Auth;
    use Intervention\Image\Facades\Image;
    use Szykra\Notifications\Flash;
    
    class User extends Authenticatable
    {
        use Notifiable;
        
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
            'user_group_id',
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
        
        /**
         * Define variable types to cast some attributes to.
         * @var array
         */
        protected $casts = [
            'show_email'   => 'boolean',
            'show_phone'   => 'boolean',
            'show_address' => 'boolean',
            'show_age'     => 'boolean',
        ];
        
        /**
         * Define the relationship with the user's group.
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function group()
        {
            return $this->belongsTo('App\UserGroup', 'user_group_id', 'id');
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
         * Add a scope for only getting active accounts.
         * @param $query
         */
        public function scopeActive($query)
        {
            $query->where('status', true);
        }
        
        /**
         * Send the password reset notification.
         * @param  string $token
         * @return void
         */
        public function sendPasswordResetNotification($token)
        {
            $this->notify(new ResetPassword($token));
        }
        
        /**
         * Add a scope for only getting BTS member accounts.
         * @param $query
         */
        public function scopeMember($query)
        {
            $query->select('users.*')
                  ->join('user_groups', 'users.user_group_id', '=', 'user_groups.id')
                  ->whereIn('user_groups.name', ['member', 'committee', 'associate']);
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
        
        /**
         * Add a scope to allow searching for users by their name, surname, nickname, username and email.
         * @param $query
         * @param $term
         */
        public function scopeSearch($query, $term)
        {
            if(stripos($term, ' ')) {
                $query->where(function ($query) use ($term) {
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
         * Test if the user is a BTS member.
         * @return bool
         */
        public function isMember()
        {
            return in_array($this->group->name, ['member', 'committee', 'associate']);
        }
        
        /**
         * Test if the user is a committee member.
         * @return bool
         */
        public function isCommittee()
        {
            return $this->group->name == 'committee';
        }
        
        /**
         * Test if the user is an associate.
         * @return bool
         */
        public function isAssociate()
        {
            return $this->group->name == 'associate';
        }
        
        /**
         * Test if the user is a staff member.
         * @return bool
         */
        public function isStaff()
        {
            return $this->group->name == 'staff';
        }
        
        /**
         * Test if the user is an admin.
         * @return bool
         */
        public function isAdmin()
        {
            return $this->isCommittee() || $this->group->name == 'super_admin';
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
         * @param string $after
         * @return string
         */
        public function getPossessiveName($after = '')
        {
            return $this->name . "'" . (substr($this->name, -1) == 's' ? '' : 's') . ' ' . $after;
        }
        
        /**
         * Get the user's role as an "account type".
         * @return int
         */
        public function getTypeAttribute()
        {
            return $this->user_group_id;
        }
        
        /**
         * Set the user's role from an "account type".
         * @param $value
         * @return bool
         */
        public function setTypeAttribute($value)
        {
            return $this->update(['user_group_id' => $value]);
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
         * Make the user a normal member.
         * @return bool
         */
        public function makeMember()
        {
            return $this->setMembershipType('member');
        }
        
        /**
         * Make the user a committee member.
         * @return bool
         */
        public function makeCommittee()
        {
            return $this->setMembershipType('committee');
        }
        
        /**
         * Make the user an associate
         * @return bool
         */
        public function makeAssociate()
        {
            return $this->setMembershipType('associate');
        }
        
        /**
         * General function for changing a user's membership type.
         * @param string $type
         * @return bool
         */
        private function setMembershipType($type = 'member')
        {
            if($this->id == Auth::user()->id) {
                Flash::warning('You cannot change your own membership type');
                
                return false;
            }
            
            
            return $this->update([
                'user_group_id' => UserGroup::where('name', $type)->first()->id,
            ]);
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
         * Check if the user has a custom profile picture.
         * @return bool
         */
        public function hasAvatar()
        {
            return file_exists($this->getAvatarPath(true, false));
        }
        
        /**
         * Get the URL or path of the user's profile picture.
         * @param bool $absolute
         * @param bool $checkExists
         * @return string
         */
        public function getAvatarPath($absolute = false, $checkExists = false)
        {
            $basePath = '/images/profiles/';
            $imgPath  = $basePath . $this->username . '.jpg';
            $path     = !$checkExists || $this->hasAvatar() ? $imgPath : ($basePath . 'blank.jpg');
            
            return $absolute ? base_path('public/' . $path) : $path;
            
        }
        
        /**
         * Change the user's profile picture and resize to 500x500.
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
    }
