<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use bnjns\FlashNotifications\Facades\Notifications;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */
    
    use ResetsPasswords {
        sendResetResponse as sendResetResponseTrait;
    }
    
    /**
     * Where to redirect users after resetting their password.
     * @var string
     */
    protected $redirectTo = '/';
    
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    
    /**
     * Overriwde the default response to include a message.
     * @param  string $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetResponse($response)
    {
        Notifications::success('Password reset');
        return $this->sendResetResponseTrait($response);
    }
}
