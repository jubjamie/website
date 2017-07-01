<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\EnquiryRequest;
use App\Mail\Contact\Enquiry;
use App\Mail\Contact\EnquiryReceipt;
use bnjns\FlashNotifications\Facades\Notifications;
use Illuminate\Support\Facades\Mail;

class EnquiryController extends Controller
{
    /**
     * Show the enquiries form.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showForm()
    {
        return view('contact.enquiries');
    }
    
    /**
     * Process the enquiries form.
     * @param \App\Http\Requests\Contact\EnquiryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process(EnquiryRequest $request)
    {
        $data = $request->all();
        
        Mail::to('bts@bath.ac.uk')
            ->queue(new Enquiry($data));
        Mail::to($request->get('email'), $request->get('name'))
            ->queue(new EnquiryReceipt($data));
        
        Notifications::success('Enquiry sent. You should receive a receipt soon.');
        return redirect()->route('home');
    }
}
