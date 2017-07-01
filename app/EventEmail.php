<?php

namespace App;

use App\Mail\Events\CrewEmail;
use App\Traits\CorrectsTimezone;
use App\Traits\Validatable;
use bnjns\FlashNotifications\Facades\Notifications;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class EventEmail extends Model
{
    use Validatable, CorrectsTimezone;
    
    /**
     * The validation rules for an event's attributes.
     * @var array
     */
    protected static $ValidationRules = [
        'header' => 'required',
        'body'   => 'required',
        'crew'   => 'required|in:core,all',
    ];
    
    /**
     * The messages for the above validation rules.
     * @var array
     */
    protected static $ValidationMessages = [
        'header.required' => 'Please enter the email subject',
        'body.required'   => 'Please enter the email message',
        'crew.required'   => 'Please select who to send the email to',
        'crew.in'         => 'Please select who to send the email to',
    ];
    
    /**
     * The attributes fillable by mass assignment.
     * @var array
     */
    protected $fillable = [
        'event_id',
        'sender_id',
        'header',
        'body',
    ];
    
    /**
     * Define the attributes to correct the timezone for.
     * @var array
     */
    protected $correct_tz = [
        'created_at',
    ];
    
    /**
     * Define the foreign key relationship with the event.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo('App\Event');
    }
    
    /**
     * Define the foreign key relationship with the user.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo('App\User', 'sender_id');
    }
    
    /**
     * Send the email to the crew.
     * @param string    $crew
     * @param \App\User $sentFrom
     * @return mixed
     */
    public function send($crew = 'all', User $sentFrom)
    {
        // Get the crew
        $crew_list = $this->event->crew();
        if($crew == 'core') {
            $crew_list = $crew_list->core();
        }
        
        // Convert to collection of users
        $crew_list = $crew_list->get()->map(function ($c) {
            return $c->user;
        });
        
        if($crew_list->count() > 0) {
            Mail::to($crew_list)->send(new CrewEmail($this, $sentFrom));
            Notifications::success('Email sent');
            return response()->json(['response' => 'Email sent']);
        } else {
            return response()->json(['error' => 'Email sent', '__error' => true], 422);
        }
    }
}