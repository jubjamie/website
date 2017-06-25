<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Szykra\Notifications\Flash;

class MemberController extends Controller
{
    /**
     * Require that the user is logged in for all methods.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * View the members dashboard.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function dash()
    {
        // TODO: Make dashboard
        return redirect()->route('member.profile');
    }
    
    /**
     * View a member's profile.
     * @param        $username
     * @param string $tab
     * @return $this
     */
    public function view($username, $tab = 'profile')
    {
        $user = User::where('username', $username)
                    ->firstOrFail();
        
        return view('members.view')->with([
            'user' => $user,
            'tab'  => $tab,
        ]);
    }
    
    /**
     * View your profile.
     * @param string                   $tab
     * @param \Illuminate\Http\Request $request
     * @return $this
     */
    public function profile($tab = 'profile', Request $request)
    {
        return view('members.view')->with([
            'user' => $request->user(),
            'tab'  => $tab,
        ]);
    }
    
    /**
     * Update the member's profile.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $this->requireAjax();
        
        $update_action = $request->get('update');
        $remove_action = $request->get('remove');
        
        if($update_action == 'personal') {
            return $this->updatePersonal($request);
        } else if($update_action == 'contact') {
            return $this->updateContact($request);
        } else if($update_action == 'avatar') {
            return $this->updateAvatar($request);
        } else if($remove_action == 'avatar') {
            return $this->removeAvatar($request->user());
        } else if($update_action == 'password') {
            return $this->updatePassword($request);
        } else if($update_action == 'privacy') {
            return $this->updatePrivacy($request);
        } else if($update_action == 'other') {
            return $this->updateOther($request);
        } else {
            return $this->ajaxError(404, 404, 'Unknown action');
        }
    }
    
    /**
     * Update the member's personal details
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function updatePersonal(Request $request)
    {
        $this->updateMemberFields($request, ['name', 'nickname', 'dob']);
        Flash::success('Changes saved');
        return $this->ajaxResponse('Changes saved');
    }
    
    /**
     * Update the member's contact details.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function updateContact(Request $request)
    {
        $this->updateMemberFields($request, ['email', 'phone', 'address']);
        Flash::success('Changes saved');
        return $this->ajaxResponse('Changes saved');
    }
    
    /**
     * Update the member's profile picture.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function updateAvatar(Request $request)
    {
        $this->authorizeGate('member');
        
        $this->validate($request, [
            'avatar' => 'required|file',
        ], [
            'avatar.required' => 'Please select an image to use',
            'avatar.file'     => 'Please select an image to use',
        ]);
        
        $request->user()->setAvatar($request->file('avatar'));
        Flash::success('Profile picture changed');
        return $this->ajaxResponse('Profile picture changed');
    }
    
    /**
     * Remove the member's avatar.
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    private function removeAvatar(User $user)
    {
        $this->authorizeGate('member');
        if($user->hasAvatar()) {
            $path = base_path('public') . $user->getAvatarUrl();
            if(is_writeable($path)) {
                unlink($path);
                Flash::success('Profile picture removed');
                return $this->ajaxResponse('Profile picture removed');
            } else {
                return $this->ajaxError(0, 422, 'Could not remove your profile picture');
            }
        }
    }
    
    /**
     * Update the member's password.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function updatePassword(Request $request)
    {
        // Validate the request
        $validator = validator($request->only('password', 'password_new', 'password_confirm'), [
            'password'         => 'required',
            'password_new'     => 'required|min:5',
            'password_confirm' => 'required|same:password_new',
        ], [
            'password.required'         => 'Please enter your current password',
            'password_new.required'     => 'Please enter your new password',
            'password_new.min'          => 'Please use at least 5 characters',
            'password_confirm.required' => 'Please confirm your password',
            'password_confirm.same'     => 'Your new passwords don\'t match',
        ]);
        // Add the check for the current password
        $validator->after(function ($validator) use ($request) {
            $check = auth()->validate([
                'email'    => $request->user()->email,
                'password' => $request->get('password'),
            ]);
            if(!$check) {
                $validator->errors()->add('password', 'Your current password is incorrect');
            }
        });
        $this->validateWith($validator);
        
        // Update
        $request->user()->update([
            'password' => bcrypt($request->get('password_new')),
        ]);
        Flash::success('Password updated');
        return $this->ajaxResponse('Password updated');
    }
    
    /**
     * Update the member's privacy settings.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function updatePrivacy(Request $request)
    {
        $this->authorizeGate('member');
        
        // Get the request data
        $data = [
            'show_email'   => $request->has('show_email'),
            'show_phone'   => $request->has('show_phone'),
            'show_address' => $request->has('show_address'),
            'show_age'     => $request->has('show_age'),
        ];
        
        // Validate
        $fields   = ['show_email', 'show_phone', 'show_address', 'show_age'];
        $rules    = User::getValidationRules($fields);
        $messages = User::getValidationMessages($fields);
        $this->validate($request, $rules, $messages);
        
        // Update
        $request->user()
                ->update($data);
    
        Flash::success('Privacy settings updated');
        return $this->ajaxResponse('Privacy settings updated');
    }
    
    /**
     * Update the member's other settings.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function updateOther(Request $request)
    {
        $this->authorizeGate('member');
        $this->updateMemberFields($request, ['tool_colours']);
        Flash::success('Changes saved');
        return $this->ajaxResponse('Changes saved');
    }
    
    /**
     * Validate and update a list of fields.
     * @param \Illuminate\Http\Request $request
     * @param array                    $fields
     * @return mixed
     */
    private function updateMemberFields(Request $request, array $fields)
    {
        // Set up the validation
        $rules    = User::getValidationRules($fields);
        $messages = User::getValidationMessages($fields);
        
        // If validating the user's email, allow their current email address.
        if(isset($rules['email'])) {
            $rules['email'] .= ',' . $request->user()->id;
        }
        
        // Validate
        $this->validate($request, $rules, $messages);
        
        // Update
        return $request->user()
                       ->update(clean($request->only($fields)));
    }
    
    /**
     * View the membership
     * @param \Illuminate\Http\Request $request
     * @return $this
     */
    public function membership(Request $request)
    {
        // Begin the query
        $members = User::active()
                       ->member()
                       ->orderBy('surname', 'ASC')
                       ->orderBy('forename', 'ASC');
        
        // Apply the search, if exists
        if($request->has('search')) {
            $members->search($request->get('search'));
        }
        
        return view('members.membership')->with('members', $members->get());
    }
}