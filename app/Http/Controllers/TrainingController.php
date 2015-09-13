<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\GenericRequest;
use App\Http\Requests\TrainingSkillRequest;
use App\TrainingAwardedSkill;
use App\TrainingCategory;
use App\TrainingSkill;
use App\TrainingSkillProposal;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Szykra\Notifications\Flash;

class TrainingController extends Controller
{
	/**
	 * Define the middleware.
	 */
	public function __construct()
	{
		$this->middleware('auth.permission:member,admin', [
			'only' => [
				'indexSkills',
				'viewSkill',
				'awardSkill',
				'revokeSkill',
			],
		]);
		$this->middleware('auth.permission:admin', [
			'only' => [
				'storeCategory',
				'updateCategory',
				'destroyCategory',
				'createSkill',
				'storeSkill',
				'updateSkill',
				'destroySkill',
				'indexProposal',
				'viewProposal',
				'processProposal',
				'viewSkillsLogs',
			],
		]);
		$this->middleware('auth.permission:member', [
			'only' => [
				'proposeSkill',
			],
		]);

		parent::__construct();
	}

	/**
	 * Create a new skill category.
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return mixed
	 */
	public function storeCategory(GenericRequest $request)
	{
		// Require ajax
		$this->requireAjax($request);

		// Validate
		$this->validate($request, [
			'name' => 'required|unique:training_categories,name',
		], [
			'name.required' => 'Please enter the category name',
			'name.unique'   => 'A category with that name already exists',
		]);

		// Create
		TrainingCategory::create([
			'name' => $request->stripped('name'),
		]);
		Flash::success('Category created');

		return Response::json(true);
	}

	/**
	 * Update the name of a category.
	 * @param                                   $id
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return mixed
	 */
	public function updateCategory($id, GenericRequest $request)
	{
		// Require ajax
		$this->requireAjax($request);

		// Get the category
		$category = TrainingCategory::find($id);
		if(!$category) {
			return $this->ajaxError('Couldn\'t find that category', 404);
		}

		// Validate
		$this->validate($request, [
			'name' => 'required|unique:training_categories,name,' . $id,
		], [
			'name.required' => 'Please enter the category name',
			'name.unique'   => 'A category with that name already exists',
		]);

		// Update
		$category->update([
			'name' => $request->stripped('name'),
		]);
		Flash::success('Category updated');

		return Response::json(true);
	}

	/**
	 * Delete a category.
	 * @param                                   $id
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return mixed
	 */
	public function destroyCategory($id, GenericRequest $request)
	{
		// Require ajax
		$this->requireAjax($request);

		// Get the category
		$category = TrainingCategory::find($id);
		if(!$category) {
			return $this->ajaxError('Couldn\'t find that category', 404);
		}

		// Delete
		$category->delete();
		Flash::success('Category deleted');

		return Response::json(true);
	}

	/**
	 * Display a list of all of the current skills.
	 * @return Response
	 */
	public function indexSkills()
	{
		// Render
		return View::make('training.skills.index');
	}

	/**
	 * Display the form for creating a skill.
	 * @return mixed
	 */
	public function createSkill()
	{
		return View::make('training.skills.create');
	}

	/**
	 * Process and store a new training skill.
	 * @param \App\Http\Requests\TrainingSkillRequest $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function storeSkill(TrainingSkillRequest $request)
	{
		// Create the skill
		TrainingSkill::create($request->stripped('name', 'description', 'requirements_level1', 'requirements_level2', 'requirements_level3') + [
				'category_id' => null,
			]);
		Flash::success('Skill created');

		return redirect(route('training.skills.index'));
	}

	/**
	 * View a skill's details
	 * @param $id
	 * @return Response
	 */
	public function viewSkill($id)
	{
		$skill = TrainingSkill::findOrFail($id);

		return View::make('training.skills.view')
		           ->with([
			           'skill'         => $skill,
			           'awarded_users' => $skill->users,
			           'awardedSkill'  => $this->user->getSkill($skill),
		           ]);
	}

	/**
	 * Update the details of a skill.
	 * @param                                   $id
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return mixed
	 */
	public function updateSkill($id, GenericRequest $request)
	{
		// Require ajax
		$this->requireAjax($request);

		// Get the skill
		$skill = TrainingSkill::find($id);
		if(!$skill) {
			return $this->ajaxError('Couldn\'t find that skill', 404);
		}

		// Check the field and value
		$field = $request->get('field');
		$value = $request->get('value');
		if(!$field) {
			return $this->ajaxError('Invalid submission', 400);
		}

		// Validate
		$validator = Validator::make([$field => $value], TrainingSkill::getValidationRules($field), TrainingSkill::getValidationMessages($field));
		if($validator->fails()) {
			return $this->ajaxError($validator->messages()->first());
		}

		// Update
		$skill->update([
			$field => $value ?: null,
		]);

		return Response::json(true);
	}

	/**
	 * Delete a skill.
	 * @param                                   $id
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return Response
	 */
	public function destroySkill($id, GenericRequest $request)
	{
		// Require ajax
		$this->requireAjax($request);

		// Get the skill
		$skill = TrainingSkill::find($id);
		if(!$skill) {
			return $this->ajaxError('Couldn\'t find that skill', 404);
		}

		// Delete
		$skill->delete();
		Flash::success('Skill deleted');

		return Response::json(true);
	}

	/**
	 * Propose a skill level
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return mixed
	 */
	public function proposeSkill(GenericRequest $request)
	{
		// Require ajax
		$this->requireAjax($request);


		// Get the skill
		$skill = TrainingSkill::find($request->get('skill_id'));
		if(!$skill) {
			return $this->ajaxError('Couldn\'t find that skill', 404);
		}

		// Validate the request
		$this->validate($request, [
			'proposed_level' => 'required|integer|in:1,2,3' . ($this->user->hasSkill($skill) ? ('|min:' . ($this->user->getSkill($skill)->level + 1)) : ''),
			'reasoning'      => 'required',
		], [
			'proposed_level.required' => 'Please select a skill level',
			'proposed_level.in'       => 'Please select a skill level',
			'proposed_level.min'      => 'Please choose a level you don\'t already have',
			'reasoning.required'      => 'Please provide reasoning or evidence for this skill level',
		]);

		// Check if they already have a proposal pending for that skill
		if(TrainingSkillProposal::where('skill_id', $skill->id)->where('user_id', $this->user->id)->notAwarded()->count() > 0) {
			return Response::json(['proposed_level' => ['You already have a proposal for this skill pending']], 422);
		}

		// Create the proposal
		$proposal = TrainingSkillProposal::create([
			'skill_id'       => $skill->id,
			'user_id'        => $this->user->id,
			'proposed_level' => $request->get('proposed_level'),
			'reasoning'      => $request->stripped('reasoning'),
			'date'           => Carbon::now(),
		]);
		Flash::success('Skill level proposed');

		// Email the training officer
		Mail::queue('emails.training.skill_proposal', [
			'proposal' => $proposal,
			'skill'    => $skill,
			'user'     => $this->user,
		], function ($message) {
			$message->to('training@bts-crew.com')
			        ->subject('Training Skill Proposal');
		});

		return Response::json(true);
	}

	/**
	 * Award a skill to a user
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return mixed
	 */
	public function awardSkill(GenericRequest $request)
	{
		// Require ajax
		$this->requireAjax($request);

		// Get the skill
		$skill = TrainingSkill::find($request->get('skill_id'));
		if(!$skill) {
			return $this->ajaxError('Couldn\'t find that skill', 404);
		}

		// Check the user's permissions
		if(!$this->user->isAdmin() && (!$this->user->hasSkill($skill) || $this->user->getSkill($skill)->level < 3)) {
			return $this->ajaxError('You need to be an admin or Level 3 to award this skill', 403);
		}

		// Validate
		$this->validate($request, [
			'user_id' => 'required|exists:users,id|not_in:' . $this->user->id,
			'level'   => 'in:1,2,3',
		], [
			'user_id.required' => 'Please select a member',
			'user_id.exists'   => 'Please select a member',
			'user_id.not_in'   => 'You can\'t award yourself a skill level',
			'level.in'         => 'Please select a level to award',
		]);

		// Check if they already have that level
		$user = User::find($request->get('user_id'));
		if($user->hasSkill($skill) && $user->getSkill($skill)->level >= $request->get('level')) {
			return Response::json(['level' => [$user->forename . ' already has that level']], 422);
		}

		// Create/update the skill level
		$date = Carbon::now();
		$user->setSkillLevel($skill->id, $request->get('level'));
		Flash::success('Skill level awarded');

		// Check to see if they have any outstanding proposals
		$proposals = TrainingSkillProposal::where('skill_id', $skill->id)
		                                  ->where('user_id', $request->get('user_id'))
		                                  ->where('proposed_level', '<=', $request->get('level'))
		                                  ->notAwarded()
		                                  ->get();
		foreach($proposals as $proposal) {
			$proposal->update([
				'awarded_level'   => $request->get('level'),
				'awarded_by'      => $this->user->id,
				'awarded_comment' => "Awarded using 'Award Skill' functionality",
				'awarded_date'    => $date,
			]);
		}

		return Response::json(true);
	}

	/**
	 * Revoke a user's skill level
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return mixed
	 */
	public function revokeSkill(GenericRequest $request)
	{
		// Require ajax
		$this->requireAjax($request);

		// Get the skill
		$skill = TrainingSkill::find($request->get('skill_id'));
		if(!$skill) {
			return $this->ajaxError("Couldn't find that skill", 404);
		}

		// Check the user's permissions
		if(!$this->user->isAdmin() && (!$this->user->hasSkill($skill) || $this->user->getSkill($skill)->level < 3)) {
			return $this->ajaxError('You need to be an admin or Level 3 to revoke this skill', 403);
		}

		// Validate the request
		$this->validate($request, [
			'skill_id' => 'exists:training_skills,id',
			'user_id'  => 'exists:users,id',
			'level'    => 'in:0,1,2',
		], [
			'skill_id.exists' => 'Please select a skill',
			'user_id.exists'  => 'Please select a member',
			'level.in'        => 'Please select a new skill level',
		]);

		// Check the user has that skill
		$user          = User::find($request->get('user_id'));
		$awarded_skill = TrainingAwardedSkill::where('skill_id', $skill->id)->where('user_id', $user->id)->first();
		if(!$awarded_skill) {
			return Response::json(['skill_id' => [$user->forename . ' doesn\'t have that skill']], 422);
		} else if($awarded_skill->level <= $request->get('level')) {
			return Response::json(['level' => ['Please select a level that\'s less than ' . $user->forename . '\'s current level']], 422);
		}

		// Revoke
		if($request->get('level') == 0) {
			$awarded_skill->delete();
			Flash::success('Skill level revoked');
		} else {
			$awarded_skill->update([
				'level'      => $request->get('level'),
				'awarded_by' => $this->user->id,
			]);
			Flash::success('Skill level reduced');
		}

		return Response::json(true);
	}

	/**
	 * View all of the proposals that exist.
	 * @return string
	 */
	public function indexProposal()
	{
		$unawarded = TrainingSkillProposal::notAwarded()
		                                  ->orderBy('date', 'DESC')
		                                  ->get();
		$awarded   = TrainingSkillProposal::awarded()
		                                  ->orderBy('date', 'DESC')
		                                  ->paginate(10);

		$this->checkPagination($awarded);

		return View::make('training.skills.proposals.index')
		           ->with([
			           'unawarded' => $unawarded,
			           'awarded'   => $awarded,
		           ]);
	}

	/**
	 * View a skill proposal for review.
	 * @param $id
	 * @return
	 */
	public function viewProposal($id)
	{
		$proposal = TrainingSkillProposal::findOrFail($id);

		return View::make('training.skills.proposals.view')
		           ->with('proposal', $proposal);
	}

	/**
	 * Process a proposal submission.
	 * @param                                   $id
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function processProposal($id, GenericRequest $request)
	{
		// Get the proposal
		$proposal = TrainingSkillProposal::findOrFail($id);

		// If the proposal is already awarded, go back to viewing
		if($proposal->isAwarded()) {
			return redirect(route('training.skills.proposal.view', $id));
		}

		// Validate the request
		$this->validate($request, [
			'awarded_level'   => 'required|in:0,1,2,3',
			'awarded_comment' => 'required_if:awarded_level,0',
		], [
			'awarded_level.required'      => 'Please select the level to award',
			'awarded_level.in'            => 'Please select the level to award',
			'awarded_comment.required_if' => 'Please provide a reason why you haven\'t awarded the requested level',
		]);

		// Update the proposal
		$proposal->update([
			'awarded_level'   => $request->get('awarded_level'),
			'awarded_by'      => $this->user->id,
			'awarded_comment' => $request->stripped('awarded_comment'),
			'awarded_date'    => Carbon::now(),
		]);

		// Update the user's skill level
		if($request->get('awarded_level') != -1) {
			$proposal->user->setSkillLevel($proposal->skill_id, $request->get('awarded_level'));
		}
		Flash::success('Skill proposal processed');

		// Email the user
		$user = $proposal->user;
		Mail::queue('emails.training.skill_proposal_processed', [
			'proposal' => $proposal,
			'awarder'  => $proposal->awarder,
			'user'     => $user->forename,
			'skill'    => $proposal->skill,
		], function ($message) use ($user) {
			$message->to($user->email, $user->name)
			        ->from('training@bts-crew.com')
			        ->subject('Skill proposal processed');
		});

		return redirect(route('training.skills.proposal.index'));
	}

	/**
	 * View the awarded skills log.
	 * @return Response
	 */
	public function viewSkillsLog()
	{
		$skills = TrainingAwardedSkill::orderBy('updated_at', 'DESC')
		                              ->paginate(15);
		$this->checkPagination($skills);

		return View::make('training.skills.log')->with('skills', $skills);
	}
}
