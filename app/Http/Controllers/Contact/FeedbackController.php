<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\FeedbackRequest;
use App\Mail\Contact\Feedback;
use bnjns\FlashNotifications\Facades\Notifications;
use Illuminate\Support\Facades\Mail;

class FeedbackController extends Controller
{
    /**
     * Show the feedback form.
     */
    public function showForm()
    {
        return view('contact.feedback');
    }
    
    /**
     * Process the feedback form.
     * @param \App\Http\Requests\Contact\FeedbackRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process(FeedbackRequest $request)
    {
        Mail::to('bts@bath.ac.uk')
            ->queue(new Feedback($request->all()));
        Notifications::success('Thank you for providing feedback');
        
        return redirect()->route('home');
    }
}
