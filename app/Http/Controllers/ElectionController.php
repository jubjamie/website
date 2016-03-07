<?php

namespace App\Http\Controllers;

use App\CommitteeRole;
use App\Election;
use App\Http\Requests;
use App\Http\Requests\ElectionRequest;
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
		$this->middleware('auth.permission:admin', [
			'except' => [
				'index',
				'view',
				'manifesto',
			],
		]);
		$this->middleware('auth.permission:member', [
			'only' => [
				'index',
				'view',
				'manifesto',
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
		           ->with('election', $election);
	}

	/**
	 * View the form to create a new election.
	 * @return mixed
	 */
	public function create()
	{
		// Determine the positions
		$positions = CommitteeRole::orderBy('order', 'ASC')->lists('name', 'id');

		return View::make('elections.create')
		           ->with('positions', $positions)
		           ->with('election', new Election(['type' => 1]))
		           ->with('route', route('elections.create.do'));
	}

	/**
	 * Process the form and create a new election.
	 * @param $request
	 * @return mixed
	 */
	public function store(ElectionRequest $request)
	{
		// Determine the positions
		$positions = $this->determineElectionPositions($request);


		// Create the election
		$election = Election::create([
			'type'              => $request->get('type'),
			'bathstudent_id'    => $request->get('bathstudent_id'),
			'hustings_time'     => $request->get('hustings_time'),
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
	 * View the form to edit an election.
	 * @param $id
	 */
	public function edit($id)
	{
		$election  = Election::findOrFail($id);
		$positions = $election->positions;

		return View::make('elections.edit')
		           ->with('positions', $positions)
		           ->with('election', $election);
	}

	/**
	 * Process the form submission and update the election.
	 * @param                                    $id
	 * @param \App\Http\Requests\ElectionRequest $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update($id, ElectionRequest $request)
	{
		$election  = Election::findOrFail($id);
		$positions = $this->determineElectionPositions($request);
		$election->update([
			'type'              => $request->get('type'),
			'bathstudent_id'    => $request->get('bathstudent_id'),
			'hustings_time'     => $request->get('hustings_time'),
			'hustings_location' => $request->stripped('hustings_location'),
			'nominations_start' => $request->get('nominations_start'),
			'nominations_end'   => $request->get('nominations_end'),
			'voting_start'      => $request->get('voting_start'),
			'voting_end'        => $request->get('voting_end'),
			'positions'         => $positions,
		]);
		Flash::success('Updated');

		return redirect()->route('elections.view', ['id' => $id]);
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

	/**
	 * Determine the positions available in an election.
	 * @param \App\Http\Requests\ElectionRequest $request
	 * @return array
	 */
	private function determineElectionPositions(ElectionRequest $request)
	{
		if($request->get('type') == 2) {
			$positions_checked = $request->get('positions_checked');
			$positions         = array_values(array_filter($request->get('positions'), function ($index) use ($positions_checked) {
				return in_array($index, $positions_checked);
			}, ARRAY_FILTER_USE_KEY));
		} else {
			$positions = CommitteeRole::orderBy('order', 'ASC')->lists('name', 'id')->toArray();
		}

		return $positions;
	}
}