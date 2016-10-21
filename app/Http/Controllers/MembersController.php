<?php
    
    namespace App\Http\Controllers;
    
    use App\Http\Requests;
    use App\Http\Requests\GenericRequest;
    use App\User;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Facades\View;
    use Szykra\Notifications\Flash;
    
    class MembersController extends Controller
    {
        /**
         * Add the permissions requirements for each route.
         */
        public function __construct()
        {
            // Require authentication
            $this->middleware('auth.permission:member', [
                'except' => [
                    'dash',
                    'getMyProfile',
                    'postMyProfile',
                    'updatePassword',
                    'dashSU',
                    'profile',
                ],
            ]);
            $this->middleware('auth', [
                'only' => [
                    'dash',
                    'dashSU',
                    'getMyProfile',
                    'postMyProfile',
                    'updatePassword',
                    'profile',
                ],
            ]);
            
            parent::__construct();
        }
        
        /**
         * Display the dashboard for BTS members.
         * @return Response
         */
        public function dash()
        {
            // If the user is an SU officer then
            // redirect to the SU dashboard
            if($this->user->isSU()) {
                return redirect(route('su.dash'));
            }
            
            // TODO: Make
            return redirect(route('members.myprofile'));
        }
        
        /**
         * Display the dashboard for SU officers.
         * @return Response
         */
        public function dashSU()
        {
            // The SU dashboard is restricted to SU
            // officers and BTS committee members
            if(!$this->user->isAdmin() && !$this->user->isSU()) {
                return redirect(route('members.dash'));
            }
            
            // TODO: Make
            return redirect(route('events.diary'));
        }
        
        /**
         * Display a member's profile page.
         * @param        $username
         * @param string $tab
         * @return \Illuminate\Http\Response
         */
        public function profile($username, $tab = 'profile')
        {
            $user = User::where('username', $username)->active()->member()->firstOrFail();
            
            return View::make('members.profile')->with([
                'user' => $user,
                'tab'  => $tab,
            ]);
        }
        
        /**
         * Display the current user's profile page.
         * @param string $tab
         * @return \Illuminate\Http\Response
         */
        public function getMyProfile($tab = 'profile')
        {
            return View::make('members.my_profile')->with([
                'user' => $this->user,
                'tab'  => $tab,
            ]);
        }
        
        /**
         * Process changes to the user's profile using AJAX.§
         * @param Request $request
         * @return Response
         */
        public function postMyProfile(Request $request)
        {
            // If the request was made by AJAX then we are updating a single
            // field. If this is the case then we need to check what's been
            // submitted, to protect against updating disallowed fields,
            // validate the value and then update the user's attribute.
            if($request->ajax()) {
                // Check a field is specified
                $field = $request->get('field') ?: @key($request->except('_token'));
                $value = $request->get('value') ?: $request->get($field);
                if(!$field) {
                    return $this->ajaxError('Invalid submission');
                }
                
                // Check that the field is allowed
                if(!in_array($field,
                    ['name', 'nickname', 'email', 'dob', 'phone', 'address', 'tool_colours', 'show_email', 'show_phone', 'show_address', 'show_age',])
                ) {
                    return $this->ajaxError('Unknown field');
                }
                
                // Only validate the input if the field isn't one of the privacy settings
                $is_privacy = in_array($field, ['show_email', 'show_phone', 'show_address', 'show_age']);
                if(!$is_privacy) {
                    $validator = Validator::make([$field => $value], User::getValidationRules($field), User::getValidationMessages($field));
                    if($validator->fails()) {
                        return $this->ajaxError($validator->messages()->first());
                    }
                }
                
                // Update
                $this->user->update([
                    $field => $is_privacy ? ($value == 'true') : ($field == 'dob' && $value ? Carbon::createFromFormat('d/m/Y', $value) : $value),
                ]);
                
                return \Illuminate\Support\Facades\Response::json(true);
            }
            // If the request was not made by ajax then we are updating
            // the user's profile picture. We want to check if we're
            // changing or uploading a photo, or simply removing it.
            else {
                if($request->get('action') == 'change-pic') {
                    $file = $request->file('avatar');
                    if(!$file) {
                        Flash::warning('Please select an image to use');
                    } else {
                        $this->user->setAvatar($file);
                        Flash::success('Profile picture changed');
                    }
                    
                    return redirect(route('members.myprofile'));
                } else if($request->get('action') == 'remove-pic') {
                    if($this->user->hasAvatar()) {
                        unlink(base_path('public') . $this->user->getAvatarUrl());
                        Flash::success("Profile picture removed");
                    }
                    
                    return redirect(route('members.myprofile'));
                }
            }
            
            // Set a default return should the request not be recognised
            App::abort(404);
        }
        
        /**
         * @param \App\Http\Requests\GenericRequest $request
         * @return mixed
         */
        public function updatePassword(GenericRequest $request)
        {
            // Validate the input
            $this->validate($request, [
                'password' => 'required|min:5|confirmed',
            ], [
                'password.required'  => 'Please enter your new password',
                'password.min'       => 'Please use at least 5 characters',
                'password.confirmed' => 'The password confirmation doesn\'t match',
            ]);
            
            // Update
            $this->user->update([
                'password' => bcrypt($request->get('password')),
            ]);
            Flash::success('Password updated');
            
            return \Illuminate\Support\Facades\Response::json(true);
        }
        
        /**
         * View the BTS membership list.
         * @param \Illuminate\Http\Request $request
         * @return \Illuminate\Http\Response
         */
        public function membership(Request $request)
        {
            // Get the search query
            $search = $this->search($request);
            
            // Query members
            $members = User::active()
                           ->member()
                           ->orderBy('surname', 'ASC')
                           ->orderBy('forename', 'ASC');
            
            // Query search
            if($search) {
                $members = $members->search($search);
            }
            
            // Get the results
            $members = $members->get();
            
            return View::make('members.membership')->with([
                'members' => $members,
                'search'  => $search,
            ]);
        }
    }
