<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use bnjns\FlashNotifications\Facades\Notifications;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    
    use AuthenticatesUsers {
        logout as logoutTrait;
    }
    
    /**
     * Where to redirect users after login.
     * @var string
     */
    protected $redirectTo = '/members';
    
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
    
    /**
     * Get the login username to be used by the controller.
     * @return string
     */
    public function username()
    {
        return 'username';
    }
    
    /**
     * Validate the user login request.
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Please enter your username or email address',
            'password.required' => 'Please enter your password',
        ]);
    }
    
    /**
     * Attempt to log the user into the application.
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $request->merge(['email' => $request->get($this->username())]);
        
        return $this->guard()->attempt($request->only('username', 'password'), true)
               || $this->guard()->attempt($request->only('email', 'password'), true);
    }
    
    /**
     * The user has been authenticated.
     * @param  \Illuminate\Http\Request $request
     * @param  mixed                    $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        Notifications::success('Logged in');
    }
    
    /**
     * Log the user out of the application.
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $response = $this->logoutTrait($request);
        Notifications::success('Logged out');
        
        return $response;
    }
}
