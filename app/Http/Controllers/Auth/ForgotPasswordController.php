<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use bnjns\FlashNotifications\Facades\Notifications;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */
    
    use SendsPasswordResetEmails;
    
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    
    /**
     * Override the default response for when sending
     * the reset email is successful.
     * @param  string $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetLinkResponse($response)
    {
        Notifications::success('A link to reset your password has been sent to the email address specified.');
        return redirect()->route('auth.login');
    }
}
