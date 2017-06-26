<?php

namespace App\Mail\Events;

use App\EventEmail;
use App\Mail\Mailable;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class CrewEmail extends Mailable
{
    use Queueable, SerializesModels;
    
    /**
     * Variable to store the data.
     * @var array
     */
    private $data;
    
    /**
     * Create a new message instance.
     * @param \App\EventEmail $email
     * @param \App\User       $user
     */
    public function __construct(EventEmail $email, User $user)
    {
        $this->data = [
            'event'      => $email->event->name,
            'from_email' => $user->email,
            'from_name'  => $user->name,
            'subject'    => $email->header,
            'body'       => $email->body,
        ];
    }
    
    /**
     * Build the message.
     * @return $this
     */
    public function build()
    {
        return $this->subject('[' . $this->data['event'] . '] ' . $this->data['subject'])
                    ->from($this->data['from_email'], $this->data['from_name'])
                    ->cc($this->data['from_email'], $this->data['from_name'])
                    ->markdown('emails.events.crew_email')
                    ->with($this->data);
    }
}
