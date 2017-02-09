<?php
    
    namespace App\Notifications\Messages;
    
    use Illuminate\Notifications\Messages\MailMessage as IlluminateMailMessage;
    
    class MailMessage extends IlluminateMailMessage
    {
        /**
         * Override the default method for setting the subject
         * to prepend with '[Backstage Website]'.
         * @param string $subject
         * @return $this
         */
        public function subject($subject)
        {
            return parent::subject('[Backstage Website] ' . $subject);
        }
    }
