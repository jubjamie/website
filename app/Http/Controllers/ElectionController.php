<?php

namespace App\Http\Controllers;

use App\CommitteeRole;
use App\Election;
use App\Http\Requests;
use App\Http\Requests\GenericRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Szykra\Notifications\Flash;

class ElectionController extends Controller
{
	/**
	 * Define the access permissions
	 * ElectionController constructor.
	 */
	public function __construct()
	{
		$this->middleware('auth.permission:member', [
			'except' => [
				'create',
				'store',
				'delete',
			],
		]);
		$this->middleware('auth.permission:admin', [
			'only' => [
				'create',
				'store',
				'delete',
			],
		]);
	}

	/**
	 * View all of the elections.
	 * @return mixed
	 */
	public function index()
	{
		$elections = Election::orderBy('voting_start', 'DESC')
		                     ->paginate(10);

		return View::make('elections.index')
		           ->with(compact('elections'));
	}

	/**
	 * View an election.
	 * @param $id
	 * @return mixed
	 */
	public function view($id)
	{
		$election = Election::findOrFail($id);

		return View::make('elections.view')
		           ->with(compact('election'));
	}

	/**
	 * View the form to create a new election.
	 * @param $request
	 * @return mixed
	 */
	public function create(GenericRequest $request)
	{
		// Determine the positions
		$positions = $request->old('positions');
		if(!is_array($positions) || empty($positions)) {
			$positions = CommitteeRole::orderBy('order', 'ASC')->lists('name', 'id');
		}

		return View::make('elections.create')
		           ->with(compact('positions'));
	}

	/**
	 * Process the form and create a new election.
	 * @param $request
	 * @return mixed
	 */
	public function store(GenericRequest $request)
	{
		// Validate the form submission
		$this->validate($request, [
			'type'              => 'required|in:' . implode(',', array_keys(Election::$Types)),
			'hustings_date'     => 'required|datetime',
			'hustings_location' => 'required',
			'nominations_start' => 'required|datetime',
			'nominations_end'   => 'required|datetime|after:nominations_start',
			'voting_start'      => 'required|datetime',
			'voting_end'        => 'required|datetime|after:voting_start',
			'positions_checked' => 'required_if:type,2|array',
			'positions'         => 'required_if:type,2|array|each:required',
		], [
			'type.required'              => 'Please select an election type.',
			'type.in'                    => 'Please select a valid election type.',
			'hustings_date.required'     => 'Please enter the date of the hustings',
			'hustings_date.datetime'     => 'Please enter a valid date',
			'hustings_location.required' => 'Please enter the hustings location',
			'nominations_start.required' => 'Please enter when the nominations open',
			'nominations_end.required'   => 'Please enter when the nominations close',
			'nominations_start.datetime' => 'Please enter a valid date for when the nominations open',
			'nominations_end.datetime'   => 'Please enter a valid date for when the nominations close',
			'nominations_end.after'      => 'The nominations have to close after they\'ve started!',
			'voting_start.required'      => 'Please enter when voting opens',
			'voting_end.required'        => 'Please enter when voting closes',
			'voting_start.datetime'      => 'Please enter a valid date for when voting opens',
			'voting_end.datetime'        => 'Please enter a valid date for when voting closes',
			'voting_end.after'           => 'Voting has to close after it\'s opened!',
			'positions_checked.required' => 'Please select at least 1 position',
			'positions_checked.array'    => 'Please select at least 1 position',
			'positions.each.required'    => 'Please enter a position title',
		]);

		// Determine the positions
		if($request->get('type') == 2) {
			$positions_checked = $request->get('positions_checked');
			$positions         = array_values(array_filter($request->get('positions'), function ($index) use ($positions_checked) {
				return in_array($index, $positions_checked);
			}, ARRAY_FILTER_USE_KEY));
		} else {
			$positions = CommitteeRole::orderBy('order', 'ASC')->lists('name', 'id')->toArray();
		}


		// Create the election
		$election = Election::create([
			'type'              => $request->get('type'),
			'hustings_time'     => $request->get('hustings_date'),
			'hustings_location' => $request->stripped('hustings_location'),
			'nominations_start' => $request->get('nominations_start'),
			'nominations_end'   => $request->get('nominations_end'),
			'voting_start'      => $request->get('voting_start'),
			'voting_end'        => $request->get('voting_end'),
			'positions'         => $positions,
		]);
		File::makeDirectory($election->getManifestoPath(), 0775, true);
		Flash::success('Election created');

		return redirect()->route('elections.view', ['id' => $election->id]);
	}

	/**
	 * Delete an election.
	 * @param $id
	 * @param $request
	 * @return mixed
	 */
	public function destroy($id, GenericRequest $request)
	{
		// Make sure AJAX
		$this->requireAjax($request);

		// Get the election
		$election = Election::find($id);
		if(!$election) {
			return $this->ajaxError('Couldn\'t find that election', 404);
		}

		// Delete
		$election->delete();
		File::delete($election->getManifestoPath());
		Flash::success('Election deleted');

		return Response::json(true);
	}

	/**
	 * Add a nomination to the election and upload the manifesto.
	 * @param $id
	 * @param $request
	 * @return mixed
	 */
	public function addNominee($id, GenericRequest $request)
	{
		// Make sure AJAX
		$this->requireAjax($request);

		// Get the election
		$election = Election::find($id);
		if(!$election) {
			return $this->ajaxError('Couldn\'t find that election.', 404);
		}

		// Check that nominations are open
		if(!$election->isNominationsOpen()) {
			return $this->ajaxError('Nominations are currently closed', 405);
		}

		// Validate the input
		$this->validate($request, [
			'user_id'   => 'required|exists:users,id',
			'position'  => 'required|in:' . implode(',', array_keys($election->positions)) . '|unique:election_nominations,position,NULL,id,election_id,'
			               . $election->id . ',user_id,' . $request->get('user_id'),
			'manifesto' => 'required|mimes:pdf',
		], [
			'user_id.required'   => 'Please select a member',
			'user_id.exists'     => 'Please select a valid member',
			'position.required'  => 'Please select a position they are running for',
			'position.in'        => 'Please select a valid position',
			'position.unique'    => 'They are already running for this position',
			'manifesto.required' => 'Please provide their manifesto. If you had, it might be too big to upload (max of 2MB).',
			'manifesto.mimes'    => 'Only PDFs are currently supported',
		]);

		// Create the nomination and upload manifesto
		$nomination = $election->nominations()->create($request->only('user_id', 'position'));
		$request->file('manifesto')->move($election->getManifestoPath(), $nomination->getManifestoName());
		Flash::success('Nomination created');

		return Response::json(true);
	}


	/**
	 * Remove a nomination.
	 * @param                                   $id
	 * @param                                   $nomineeId
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return mixed
	 */
	public function removeNominee($id, $nomineeId, GenericRequest $request)
	{
		// Make sure AJAX
		$this->requireAjax($request);

		// Get the election
		$election = Election::find($id);
		if(!$election) {
			return $this->ajaxError('Couldn\'t find that election', 404);
		}

		// Check that nominations are open
		if(!$election->isNominationsOpen()) {
			return $this->ajaxError('Nominations are currently closed', 405);
		}

		// Get the nomination
		$nomination = $election->nominations()->where('id', $nomineeId)->first();
		if(!$nomination) {
			return $this->ajaxError('Couldn\'t find that nomination', 404);
		}

		// Delete
		$nomination->delete();
		File::delete($nomination->getManifestoPath());
		Flash::success('Nomination deleted');

		return Response::json(true);
	}

	/**
	 * Set the elected committee members.
	 * @param $id
	 * @param $request
	 * @return mixed
	 */
	public function elect($id, GenericRequest $request)
	{
		// Make sure AJAX
		$this->requireAjax($request);

		// Get the election
		$election = Election::find($id);
		if(!$election) {
			return $this->ajaxError('Couldn\'t find that election', 404);
		}

		// Check that voting has closed
		if(!$election->hasVotingClosed()) {
			return $this->ajaxError('Voting has not yet closed', 405);
		}

		// Validate the request
		$this->validate($request, [
			'elected' => 'array|each:required',
		], [
			'elected.array'         => 'Please select the elected members',
			'elected.each.required' => 'Please select the elected members',
		]);

		// Set those elected
		$elected = $request->get('elected') ?: [];
		foreach($election->nominations as $nomination) {
			$nomination->update(['elected' => in_array($nomination->id, $elected)]);
		}
		Flash::success('Saved');

		return Response::json(true);
	}

	/**
	 * View a nominee's manifesto.
	 * @param $id
	 * @param $nomineeId
	 * @return string
	 */
	public function manifesto($id, $nomineeId)
	{
		$election   = Election::findOrFail($id);
		$nomination = $election->nominations()->where('id', $nomineeId)->first();
		$path       = $nomination->getManifestoPath();
		if(!$nomination || !file_exists($path)) {
			App::abort(404);
		}

		return Response::make(file_get_contents($path), 200, [
			'Content-Type'        => 'application/pdf',
			'Content-Disposition' => 'inline; filename="' . $nomination->getManifestoName() . '"',
			'Content-Length'      => filesize($path),
		]);
	}
}