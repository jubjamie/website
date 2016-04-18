<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\GenericRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Szykra\Notifications\Flash;

class UsersController extends Controller
{
	/**
	 * The list of allowed bulk actions.
	 * @var array
	 */
	public static $BulkActions = [
		'archive'   => 'Archive',
		'committee' => 'Make committee',
		'associate' => 'Make associate',
	];

	/**
	 * Set up the middleware.
	 */
	public function __construct()
	{
		$this->middleware('auth.permission:admin');
		parent::__construct();
	}

	/**
	 * Display the listing of users.
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$filter = $this->filter($request);
		$search = $this->search($request);

		// Initialise query
		$users = User::nameOrder();

		// Set any filter
		if($filter == 'all') {
			$users = $users->get();
		} else if($filter == 'archived') {
			$users = $users->where('status', 0)
			               ->get();
		} else if($filter == 'active') {
			$users = $users->where('status', 1)
			               ->get();
		} else if(in_array($filter, ['member', 'committee', 'associate', 'su'])) {
			$users = $users->select('users.*')
			               ->join('role_user', 'users.id', '=', 'role_user.user_id')
			               ->join('roles', 'role_user.role_id', '=', 'roles.id')
			               ->where('roles.name', '=', $filter)
			               ->where('status', 1)
			               ->get();
		} else {
			// we all love katie lots <3
			if($search) {
				$users->search($search);
			}

			// Paginate results
			$users = $users->paginate(15);
			$this->checkPagination($users);
		}

		return View::make('users.index')->with([
			'filter' => $filter,
			'search' => $search,
			'users'  => $users,
		]);
	}

	/**
	 * Process and perform the bulk user actions
	 * @param Request $request
	 * @return Response
	 */
	public function bulkUpdate(Request $request)
	{
		// If 'archive-user' exists then the user has selected
		// to archive a single user. In this case just get
		// the user and perform the archive() method
		if($request->has('archive-user')) {
			$user = User::find($request->get('archive-user'));
			if($user && $user->archive()) {
				Flash::success("'{$user->name}' was successfully archived");
			}
		}
		// If the user has selected the bulk option check the chosen
		// option and the existence of some users to modify.
		// Once validated perform the action on each user.
		else if($request->has('bulk')) {
			$action = $request->get('bulk-action');
			$users  = $request->get('users');

			// Check the inputs
			if(!$action || !in_array($action, array_keys(self::$BulkActions))) {
				Flash::warning('Please select a valid action to perform');
			} else if(!$users || !is_array($users) || count($users) == 0) {
				Flash::warning('Please select some users to perform the action on');
			} else {
				// Process each user
				$success = 0;
				foreach($users as $id) {
					$user = User::find($id);
					if($user
					   && (($action == 'archive' && $user->archive())
					       || ($action == 'committee') && $user->makeCommittee()
					       || ($action == 'associate') && $user->makeAssociate())
					) {
						$success++;
					}
				}

				// Flash messages
				$msg = $action == 'archive' ? 'archived' : ($action == 'committee' ? 'made committee members' : 'made associates');
				if($success == count($users)) {
					Flash::success(sprintf("All of the selected users were succesfully %s", $msg));
				} else if($success > 0) {
					Flash::success(sprintf("%s of the %s users were successfully %s", $success, count($users), $msg));
				}
			}
		}

		return redirect(route('user.index'));
	}

	/**
	 * Show the form for creating a single new user.
	 * @return Response
	 */
	public function create()
	{
		// Check to see if we have been given a summary from the bulk add process.
		// If this is the case then render the results view instead of the form.
		$results = Session::pull('bulkResults');
		if($results) {
			return View::make('users.bulk_summary')->with('results', $results);
		} else {
			return View::make('users.create');
		}
	}

	/**
	 * Store a newly created user in storage.
	 * @param  Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		// Define the validation rules and messages
		$rules    = [
			'name'     => 'required|name',
			'username' => 'required|regex:/[a-zA-Z0-9_\.]+/|unique:users',
			'type'     => 'required|in:' . implode(',', array_keys(User::$CreateAccountTypes)),
		];
		$messages = [
			'name.required'      => 'Please enter the new user\'s name',
			'name.name'          => 'Please enter their forename and surname',
			'username.required'  => 'Please enter the new user\'s BUCS username',
			'username.regex'     => 'Please enter their username using just letters and numbers',
			'username.unique'    => 'A user with that username already exists',
			'type.required'      => 'Please select an account type',
			'type.in'            => 'Please select one of the provided account types',
		];

		// If single user mode has been selected then simply create the
		// validator and run it on the single user form inputs. If the
		// validation fails then go back with the necessary input. If
		// it succeeds then simply create the new user account
		if($request->get('mode') == 'single') {
			// Validate
			$validator = Validator::make($request->only('name', 'username', 'type'), $rules, $messages);
			if($validator->fails()) {
				return redirect()->back()
				                 ->withErrors($validator)
				                 ->withInput($request->only('name', 'username', 'type', 'mode'));
			}

			// Create
			User::create($request->only('name', 'username', 'type'));
			Flash::success('User created');

			return redirect(route('user.index'));
		}
		// If bulk user mode has been selected then basic regex validation will be performed
		// on the entire input, just to check that input has been provided, before moving
		// onto analysing each user individually. This involves validating the inputs
		// against the actual rules before trying to create each user separately.
		// One user failing either stage won't affect any other user attempts.
		else if($request->get('mode') == 'bulk') {
			// Validate initially
			$validator = Validator::make($request->only('users', 'type'), [
				'type'  => $rules['type'],
				'users' => 'required|regex:/^[a-z]+\s[a-z]+[,][a-z0-9_\.]+$/im',
			], $messages + [
					'users.required' => 'Please enter the list of users to add',
					'users.regex'    => 'Please enter each user in the format specified in the <a href="#" data-toggle="modal" data-target="#helpModal">help</a>.',
				]);
			if($validator->fails()) {
				return redirect()->back()
				                 ->withErrors($validator)
				                 ->withInput($request->only('users', 'type', 'mode'));
			}

			// Sanitise each user input
			$userDetails = array_map(function ($value) {
				return trim($value);
			}, explode(PHP_EOL, $request->get('users')));

			// Try each user
			$results = [];
			foreach($userDetails as $i => $user) {
				$data = ['type' => $request->get('type')];
				list($data['name'], $data['username']) = explode(',', $user);
				$validator = Validator::make($data, $rules, $messages);
				if($validator->fails()) {
					$results[$i] = [
						'success'  => false,
						'username' => $data['username'] ?: $i,
						'message'  => implode(PHP_EOL, array_flatten($validator->messages()->getMessages())),
					];
				} else {
					if($user = User::create($data)) {
						$results[$i] = [
							'success'  => true,
							'username' => $user->username ?: $i,
							'message'  => 'User created successfully',
						];
					} else {
						$request[$i] = [
							'success'  => false,
							'username' => $user->username ?: $i,
							'message'  => 'Something went wrong when adding this user. Consult the logs for more information.',
						];
					}
				}
			}

			// Redirect to summary
			return redirect()->back()->with('bulkResults', $results);
		}
		// If the mode isn't recognised then
		// simply go to the user index page
		else {
			return redirect(route('user.index'));
		}
	}

	/**
	 * Show the form for editing a user.
	 * @param  string $username
	 * @return Response
	 */
	public function edit($username)
	{
		$user       = User::where('username', $username)->firstOrFail();
		$ownAccount = $user->id == $this->user->id;

		return View::make('users.edit')->with([
			'user'       => $user,
			'ownAccount' => $ownAccount,
		]);
	}

	/**
	 * Update the specified user in storage.
	 * @param  string        $username
	 * @param GenericRequest $request
	 * @return \Illuminate\Http\Response
	 */
	public function update($username, GenericRequest $request)
	{
		$user       = User::where('username', $username)->firstOrFail();
		$ownAccount = $user->id == $this->user->id;

		// If performing a general save then validate the inputs and perform the
		// update, setting the account type as necessary. If updating the active
		// user, the restricted attributes will be set to their current values.
		if($request->get('action') == 'save') {
			$data        = $request->stripped('name', 'username', 'nickname', 'email', 'phone', 'dob', 'address', 'tool_colours', 'type') + [
					'show_email'   => $request->has('show_email'),
					'show_phone'   => $request->has('show_phone'),
					'show_address' => $request->has('show_address'),
					'show_age'     => $request->has('show_age'),
				];
			$data['dob'] = $data['dob'] ?: null;
			if($ownAccount) {
				$data['username'] = $user->username;
				$data['type']     = $user->type;
			}

			$validator = Validator::make($data, $user->getProfileValidationRules(), $user->getProfileValidationMessages());
			if($validator->fails()) {
				return redirect()->back()
				                 ->withInput($data)
				                 ->withErrors($validator);
			} else if($user->update($data)) {
				Flash::success('User updated');

				return redirect(route('user.index'));
			} else {
				Flash::error('Something went wrong while updating the user');

				return redirect(route('user.edit', $username));
			}
		}
		// If archiving check the user is not
		// the active user and then archive
		else if($request->get('action') == 'archive') {
			if($ownAccount) {
				Flash::warning('You cannot archive your own account');
			} else if($user->archive()) {
				Flash::success('User archived');
			} else {
				Flash::error('Something went wrong when archiving the user');
			}

			return redirect(route('user.edit', $username));
		}
		// If unarchiving then set the status and save.
		// There's no need to check the user as
		// an archived user cannot log in
		else if($request->get('action') == 'unarchive') {
			if($user->update(['status' => true])) {
				Flash::success('User unarchived');
			} else {
				Flash::error('Something went wrong when unarchiving the user');
			}

			return redirect(route('user.edit', $username));
		}
		// To change the user's profile picture
		// check the input and call the method
		// which also does the resizing.
		else if($request->get('action') == 'change-pic') {
			$file = $request->file('avatar');
			if(!$file) {
				Flash::warning('Please select an image to use');
			} else {
				$user->setAvatar($file);
				Flash::success('Profile picture changed');
			}

			return redirect(route('user.edit', $username));
		}
		// To delete the user's profile picture check
		// that PHP has permission and then unlink
		else if($request->get('action') == 'remove-pic') {
			if($user->hasAvatar()) {
				$path = base_path('public') . $user->getAvatarUrl();
				if(is_writeable($path)) {
					unlink($path);
					Flash::success("Profile picture removed");
				} else {
					Flash::error("The user's picture is not writeable");
				}
			}

			return redirect(route('user.edit', $username));
		}
		// When resetting the user's password, hash a
		// randomly generated string and email it
		else if($request->get('action') == 'reset-password') {
			$password = str_random(15);
			$user->update([
				'password' => bcrypt($password),
			]);
			Flash::success('New password sent');

			Mail::queue('emails.users.reset_password', [
				'name'     => $user->forename,
				'password' => $password,
			], function ($message) use ($user) {
				$message->subject('Your new password')
				        ->to($user->email, $user->name);
			});

			return redirect(route('user.edit', $username));
		} // Unknown action
		else {
			return redirect(route('user.edit', $username));
		}
	}
}
