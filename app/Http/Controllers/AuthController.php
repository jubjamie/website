<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Szykra\Notifications\Flash;

class AuthController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesUsers, ThrottlesLogins, ResetsPasswords {
		AuthenticatesUsers::redirectPath insteadof ResetsPasswords;
	}

	/**
	 * The name of the 'username' attribute (for authentication).
	 * @var string
	 */
	protected $username = 'username';

	/**
	 * The path of the login form.
	 * @var string
	 */
	protected $loginPath = 'login';

	/**
	 * The path to redirect to on logout (login is overridden using authenticated()).
	 * @var string
	 */
	protected $redirectPath = '';

	/**
	 * Create a new authentication controller instance.
	 */
	public function __construct()
	{
		$this->middleware('guest', ['except' => 'getLogout']);
		parent::__construct();
	}

	/**
	 * Flash a message on successful authenticated.
	 * @return \Illuminate\Http\Response
	 */
	public function authenticated()
	{
		// Flash message and redirect to the dashboard
		Flash::success('Success', 'You were logged in successfully.');

		return redirect()->intended('members');
	}

	/**
	 * Handle a login request to the application.
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function postLogin(Request $request)
	{
		$this->validate($request, [
			$this->loginUsername() => 'required', 'password' => 'required',
		], [
			$this->loginUsername() . '.required' => 'Please enter your username or email address',
			'password.required'                  => 'Please enter your password',
		]);

		// If the class is using the ThrottlesLogins trait, we can automatically throttle
		// the login attempts for this application. We'll key this by the username and
		// the IP address of the client making these requests into this application.
		$throttles = $this->isUsingThrottlesLoginsTrait();

		if($throttles && $this->hasTooManyLoginAttempts($request)) {
			return $this->sendLockoutResponse($request);
		}

		$credentials = $request->only('password');
		if(Auth::attempt(['username' => $request->get($this->loginUsername())] + $credentials, $request->has('remember'))) {
			return $this->handleUserWasAuthenticated($request, $throttles);
		} else if(Auth::attempt(['email' => $request->get($this->loginUsername())] + $credentials, $request->has('remember'))) {
			return $this->handleUserWasAuthenticated($request, $throttles);
		}

		// If the login attempt was unsuccessful we will increment the number of attempts
		// to login and redirect the user back to the login form. Of course, when this
		// user surpasses their maximum number of attempts they will get locked out.
		if($throttles) {
			$this->incrementLoginAttempts($request);
		}

		return redirect($this->loginPath())
			->withInput($request->only($this->loginUsername(), 'remember'))
			->withErrors([
				$this->loginUsername() => $this->getFailedLoginMessage(),
			]);
	}

	/**
	 * Override the default method to provide a flash message on success.
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function postReset(Request $request)
	{
		$this->validate($request, [
			'token'    => 'required',
			'email'    => 'required|email',
			'password' => 'required|confirmed',
		]);

		$credentials = $request->only(
			'email', 'password', 'password_confirmation', 'token'
		);

		$response = Password::reset($credentials, function ($user, $password) {
			$this->resetPassword($user, $password);
		});

		switch($response) {
			case Password::PASSWORD_RESET:
				Flash::success('Success', 'Your password was changed successfully and you are now logged in.');

				return redirect($this->redirectPath());

			default:
				return redirect()->back()
				                 ->withInput($request->only('email'))
				                 ->withErrors(['summary' => trans($response)]);
		}
	}
}
