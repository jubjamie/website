<?php

namespace App\Http\Controllers;

use App\Event;
use App\EventTime;
use App\Http\Requests;
use App\Http\Requests\EventRequest;
use App\Http\Requests\EventTimeRequest;
use App\Http\Requests\GenericRequest;
use App\User;
use Carbon\Carbon;
use Eluceo\iCal\Component\Calendar;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Szykra\Notifications\Flash;

class EventsController extends Controller
{
	/**
	 * Set up the middleware permissions.
	 */
	public function __construct()
	{
		$this->middleware('auth.permission:admin', [
			'only' => [
				'create',
				'store',
				'index',
				'destroy',
			],
		]);
		$this->middleware('auth.permission:member', [
			'only' => [
				'signup',
				'toggleVolunteer',
				'myDiary',
			],
		]);
		$this->middleware('auth.permission:member,admin', [
			'only' => [
				'update',
				'memberDiary',
			],
		]);

		parent::__construct();
	}

	/**
	 * View the form to add an event.
	 * @return mixed
	 */
	public function create()
	{
		return View::make('events.create');
	}

	/**
	 * View the event's diary
	 * @param null $year
	 * @param null $month
	 * @return Response
	 */
	public function diary($year = null, $month = null)
	{
		// Get the dates
		$date = $this->getDiaryDate($year, $month);

		// Get the events
		$events = $this->getEventsInMonth($date);

		// Build the calendar
		return $this->renderEventsDiary($date, $events);
	}

	/**
	 * Display the diary of a member.
	 * @param      $username
	 * @param null $year
	 * @param null $month
	 * @return mixed
	 */
	public function memberDiary($username, $year = null, $month = null)
	{
		// Get the member
		$member = User::where('username', $username)->member()->firstOrFail();

		// Get the dates
		$date = $this->getDiaryDate($year, $month);

		// Get the events
		$events = $this->getEventsInMonth($date, $member);

		// Render
		return $this->renderEventsDiary($date,
			$events,
			$member->getPossessiveName('Diary'),
			route('events.memberdiary', ['username' => $username, 'year' => '%year', 'month' => '%month'])
		);
	}

	/**
	 * Display the current user's diary.
	 * @param null $year
	 * @param null $month
	 * @return mixed
	 */
	public function myDiary($year = null, $month = null)
	{
		// Get the dates
		$date = $this->getDiaryDate($year, $month);

		// Get the events
		$events = $this->getEventsInMonth($date, $this->user);

		// Render
		return $this->renderEventsDiary($date,
			$events,
			'My Diary',
			route('events.mydiary', ['year' => '%year', 'month' => '%month'])
		);
	}

	/**
	 * View a list of events.
	 * @return mixed
	 */
	public function index()
	{
		$events = Event::select('events.*')
		               ->join('event_times', 'events.id', '=', 'event_times.event_id')
		               ->orderBy('event_times.start', 'DESC')
		               ->distinct()
		               ->distinctPaginate(15);
		$this->checkPagination($events);

		return View::make('events.index')->withEvents($events);
	}

	/**
	 * View the events needing an EM or crew.
	 * @param null $tab
	 * @return \Illuminate\Support\Facades\Response
	 */
	public function signup($tab = null)
	{
		// Get the event lists depending on whether we
		// are viewing the em tab or the crew tab
		if($tab == 'crew') {
			$list = Event::future()
			             ->leftJoin('event_crew', 'events.id', '=', 'event_crew.event_id')
			             ->whereNull('event_crew.event_id')
			             ->orderBy('event_times.start', 'ASC')
			             ->distinctPaginate(15);
		} else {
			$list = Event::whereNull('em_id')
			             ->future()
			             ->orderBy('event_times.start', 'ASC')
			             ->distinctPaginate(15);
		}

		return View::make('events.signup')->with([
			'list' => $list,
			'em'   => $tab != 'crew',
		]);
	}

	/**
	 * Add an event to the diary.
	 * @param \App\Http\Requests\EventRequest $request
	 * @return \Illuminate\Support\Facades\Response
	 */
	public function store(EventRequest $request)
	{
		// Create the event
		$event = Event::create($request->stripped('name', 'venue', 'description', 'type', 'client_type', 'venue_type') + [
				'em_id'              => $request->get('em_id') ?: null,
				'description_public' => $request->get('desc_public') ? $request->stripped('description') : '',
				'crew_list_status'   => 1,
				'paperwork'          => [
					'risk_assessment'  => false,
					'insurance'        => false,
					'finance_em'       => false,
					'finance_treas'    => false,
					'event_report'     => false,
				],
			]);

		// Set the event time limits
		$start_time = Carbon::createFromFormat('H:i', $request->get('time_start'));
		$end_time   = Carbon::createFromFormat('H:i', $request->get('time_end'));
		$date_start = Carbon::createFromFormat('d/m/Y', $request->get('date_start'))->setTime(0, 0, 0);
		$date_end   = Carbon::createFromFormat('d/m/Y', $request->get($request->has('one_day') ? 'date_start' : 'date_end'))->setTime(23, 59, 59);

		// Create each event time
		$date = clone $date_start;
		while($date->lte($date_end)) {
			$event->times()->save(new EventTime([
				'name'  => $event->name,
				'start' => $date->setTime($start_time->hour, $start_time->minute, 0)->toDateTimeString(),
				'end'   => $date->setTime($end_time->hour, $end_time->minute, 0)->toDateTimeString(),
			]));
			$date->setTime(0, 0, 0);
			$date->day++;
		}

		// Add the event to the finance Db
		$this->addEventToFinanceDb($event);

		// Send the email to alison if the event is non-SU and off-campus
		if($event->client_type > 1 && $event->venue_type == 2) {
			// TODO: uncomment
			//Mail::queue('emails.events.notify_alison', [
			//	'event_name'  => $event->name,
			//	'event_dates' => $event->start_date . ($request->has('one_day') ? '' : (' &ndash; ' . $event->end_date)),
			//	'em'          => $event->em_id ? $event->em->name : '<em>&ndash; not yet decided &ndash;</em>',
			//	'client'      => $event->client,
			//	'venue'       => $event->venue,
			//	'venue_type'  => Event::$VenueTypes[$event->venue_type],
			//	'description' => $event->description,
			//], function ($message) {
			//	$message->subject('Backstage External Off-Campus Event')
			//	        ->to('a.j.fleet@bath.ac.uk')
			//	        ->cc('bts@bath.ac.uk')
			//	        ->from('pm@bts-crew.com');
			//});
		}

		// Create a flash message and redirect
		Flash::success('Event created');

		return redirect(route('events.diary', [
			'year'  => $date_start->year,
			'month' => $date_start->month,
		]));
	}

	/**
	 * Send the request to add an event to the Finance Db.
	 * @param \App\Event $event
	 */
	private function addEventToFinanceDb(Event $event)
	{
		if($event->type == Event::TYPE_EVENT) {
			$fields       = [
				'data[Event][event_name]'  => $event->name,
				'data[Event][start_date]'  => $event->start_date,
				'data[Event][end_date]'    => $event->end_date,
				'data[Event][verified]'    => 0,
				'data[Event][bts_crew_id]' => $event->id,
			];
			$field_string = http_build_query($fields);

			// Send the data using cURL
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, env('FINANCE_DB_ADD_URL'));
			curl_setopt($ch, CURLOPT_POST, count($fields));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
			curl_exec($ch);
			curl_close($ch);
		}
	}

	/**
	 * Update the details of an event.
	 * @param                                   $id
	 * @param                                   $action
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return Response
	 */
	public function update($id, $action, GenericRequest $request)
	{
		// Make sure the request is AJAX
		$this->requireAjax($request);

		// Get the event
		$event = Event::find($id);
		if(!$event) {
			return $this->ajaxError("Couldn't find the event", 404);
		}

		// Check that the user is either the EM or an admin
		if(!$this->user->isAdmin() && !$event->isEM($this->user, false)) {
			return $this->ajaxError('You need to be the EM or an admin to do that', 403);
		}

		switch($action) {
			case 'add-time':
				return $this->update_AddTime($request, $event);
			case 'update-time':
				return $this->update_EditTime($request, $event);
			case 'delete-time':
				return $this->update_DeleteTime($request, $event);
			case 'add-crew':
				return $this->update_AddCrew($request, $event);
			case 'update-crew':
				return $this->update_EditCrew($request, $event);
			case 'delete-crew':
				return $this->update_DeleteCrew($request, $event);
			case 'update-details':
				return $this->update_UpdateDetails($request, $event);
			case 'paperwork':
				return $this->update_Paperwork($request, $event);
			default:
				return $this->ajaxError('Unknown action', 404);
		}
	}

	/**
	 * View an event.
	 * @param $id
	 * @return Response
	 */
	public function view($id)
	{
		// Get the event
		$event = Event::findOrFail($id);

		// Check the user can view it
		if($event->type != Event::TYPE_EVENT && !($this->user->isMember() || $this->user->isAdmin())) {
			App::abort(403);
		}

		// Get a list of users not signed up
		$users_crew = User::active()->member()->nameOrder()->notCrewingEvent($event)->getSelect();
		$users_em   = User::active()->member()->nameOrder()->getSelect();

		// Create the 'mailto' link for sending event emails
		$mailto_link = 'mailto:';
		foreach($event->crew as $crew) {
			$mailto_link .= $crew->user->email . ',';
		}

		return View::make('events.view')->with([
			'event'       => $event,
			'user'        => $this->user,
			'users_crew'  => $users_crew,
			'users_em'    => $users_em,
			'isMember'    => $this->user->isMember(),
			'isAdmin'     => $this->user->isAdmin(),
			'isEM'        => $event->isEM($this->user),
			'canEdit'     => $this->user->isAdmin() || $event->isEM($this->user, false),
			'crew_emails' => $mailto_link,
		]);
	}

	/**
	 * Delete an event from the diary.
	 * @param                                   $id
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return mixed
	 */
	public function destroy($id, GenericRequest $request)
	{
		// Require ajax
		$this->requireAjax($request);

		// Get the event
		$event = Event::find($id);
		if(!$event) {
			return $this->ajaxError("Couldn't find that event", 404);
		}

		// Delete
		$event->delete();
		Flash::success('Event deleted');

		return Response::json(true);
	}

	/**
	 * Export the events diary to iCal.
	 * @return Response
	 */
	public function export()
	{
		// Create the calendar
		$calendar = new Calendar('www.bts-crew.com');
		$calendar->setName('Backstage Diary');

		// Get all the events and add each time
		$events = Event::where('type', Event::TYPE_EVENT)->get();
		foreach($events as $event) {
			foreach($event->times as $time) {
				$cal_event = new \Eluceo\iCal\Component\Event();
				$cal_event->setDtStart($time->start)
				          ->setDtEnd($time->end)
				          ->setSummary($time->event->name . ' - ' . $time->name)
				          ->setLocation($time->event->venue);
				$calendar->addComponent($cal_event);
			}
		}

		// Respond
		return (new HttpResponse($calendar->render(), 200))
			->header('Content-Type', 'text/calendar; charset=utf-8')
			->header('Content-Disposition', 'attachment; filename="bts_diary.ics"');
	}

	/**
	 * @param                                   $id
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return \Illuminate\Support\Facades\Response
	 */
	public function toggleVolunteer($id, GenericRequest $request)
	{
		// Require ajax
		$this->requireAjax($request);

		// Get the event
		$event = Event::findOrFail($id);

		// Test if they are the EM
		if($event->isEM($this->user)) {
			return $this->ajaxError('You can\'t unvolunteer as you are the EM!');
		}

		// Test if the user is already crew
		$crew = $event->crew->where('user_id', $this->user->id)->first();
		if($crew) {
			$crew->delete();
			Flash::success('You have unvolunteered');
		} else {
			$event->crew()->create([
				'name'    => null,
				'user_id' => $this->user->id,
			]);
			Flash::success('You have volunteered');

			// Send the email to the crew
			$user = $this->user;
			Mail::queue('emails.events.volunteered_crew', [
				'event' => $event->name,
				'user'  => $user->forename,
				'em'    => $event->em_id ? $event->em->name : '',
			], function ($message) use ($user, $event) {
				$message->to($user->email, $user->name)
				        ->subject("Volunteered to crew event '{$event->name}'");
			});

			// Send the email to the EM
			if($event->em_id) {
				$em = $event->em;
				Mail::queue('emails.events.volunteered_em', [
					'em'    => $em->forename,
					'user'  => $user->name,
					'event' => $event->name,
				], function ($message) use ($em, $user, $event) {
					$message->to($em->email, $em->name)
					        ->from($user->email, $user->name)
					        ->subject("Crew volunteered for '{$event->name}'");
				});
			}
		}

		return Response::json(true);
	}

	/**
	 * Add a new event time.
	 * @param \App\Http\Requests\GenericRequest $request
	 * @param \App\Event                        $event
	 * @return \Illuminate\Support\Facades\Response
	 */
	private function update_AddTime(GenericRequest $request, Event $event)
	{
		// Validate
		$this->validateEventTime($request);

		// Create the time
		$event->times()->create([
			'name'  => $request->get('name'),
			'start' => Carbon::createFromFormat('d/m/Y H:i', $request->get('date') . ' ' . $request->get('start_time')),
			'end'   => Carbon::createFromFormat('d/m/Y H:i', $request->get('date') . ' ' . $request->get('end_time')),
		]);

		Flash::success('Event time created');

		return Response::json(true);
	}

	/**
	 * Update the details of an event time.
	 * @param \App\Http\Requests\GenericRequest $request
	 * @param \App\Event                        $event
	 * @return Reponse
	 */
	private function update_EditTime(GenericRequest $request, Event $event)
	{
		// Get the event time
		$time = $event->times()->find($request->get('id'));
		if(!$time) {
			return $this->ajaxError("Couldn't find the event time", 404);
		}

		// Validate
		$this->validateEventTime($request);

		// Update
		$time->update([
			'name'  => $request->get('name'),
			'start' => Carbon::createFromFormat('d/m/Y H:i', $request->get('date') . ' ' . $request->get('start_time')),
			'end'   => Carbon::createFromFormat('d/m/Y H:i', $request->get('date') . ' ' . $request->get('end_time')),
		]);

		Flash::success('Event time updated');

		return Response::json(true);
	}

	/**
	 * Delete an event time.
	 * @param \App\Http\Requests\GenericRequest $request
	 * @param \App\Event                        $event
	 * @return Response
	 */
	private function update_DeleteTime(GenericRequest $request, Event $event)
	{
		// Get the event time
		$time = $event->times()->find($request->get('id'));
		if(!$time) {
			return $this->ajaxError("Couldn't find the event time", 404);
		} else {
			$time->delete();
			Flash::success('Event time deleted');

			return Response::json(true);
		}
	}

	/**
	 * Add a user to the crew.
	 * @param \App\Http\Requests\GenericRequest $request
	 * @param \App\Event                        $event
	 * @return mixed
	 */
	private function update_AddCrew(GenericRequest $request, Event $event)
	{
		// Validate
		$this->validateCrew($request, true);

		// Check if the user is already crewing
		$user = User::find($request->get('user_id'));
		if($event->isCrew($user)) {
			return $this->ajaxError("That user is already on the crew", 422);
		}

		// Create
		$event->crew()->create([
			'user_id' => $user->id,
			'name'    => $request->get('core') ? $request->get('name') : null,
			'em'      => $request->get('core') ? $request->has('em') : false,
		]);

		// Send the email
		Mail::queue('emails.events.add_crew', [
			'event' => $event->name,
			'user'  => $user->forename,
			'em'    => $event->em_id ? $event->em->name : '',
		], function ($message) use ($user, $event) {
			$message->to($user->email, $user->name)
			        ->subject("Volunteered to crew event '{$event->name}'");
		});

		Flash::success('Crew role created');

		return Response::json(true);
	}

	/**
	 * Update a user's crew role.
	 * @param \App\Http\Requests\GenericRequest $request
	 * @param \App\Event                        $event
	 * @return mixed
	 */
	private function update_EditCrew(GenericRequest $request, Event $event)
	{
		// Get the event crew
		$crew = $event->crew()->find($request->get('id'));
		if(!$crew) {
			return $this->ajaxError("Couldn't find the crew entry", 404);
		}

		// Validate
		$this->validateCrew($request, false);

		// Update
		$crew->update([
			'name' => $request->get('core') ? $request->get('name') : null,
			'em'   => $request->get('core') ? $request->has('em') : false,
		]);
		Flash::success('Crew role updated');

		return Response::json(true);
	}

	/**
	 * Delete a crew role.
	 * @param \App\Http\Requests\GenericRequest $request
	 * @param \App\Event                        $event
	 * @return mixed
	 */
	private function update_DeleteCrew(GenericRequest $request, Event $event)
	{
		// Get the event crew
		$crew = $event->crew()->find($request->get('id'));
		if(!$crew) {
			return $this->ajaxError("Couldn't find the crew entry", 404);
		}

		// Delete
		$crew->delete();
		Flash::success('Crew role deleted');

		return Response::json(true);
	}

	/**
	 * Update the event's venue
	 * @param \App\Http\Requests\GenericRequest $request
	 * @param \App\Event                        $event
	 * @return mixed
	 */
	private function update_UpdateDetails(GenericRequest $request, Event $event)
	{
		// Check a field is specified
		$field = $request->get('field') ?: @key($request->except('_token'));
		$value = $request->get('value') ?: $request->get($field);
		if(!$field) {
			return $this->ajaxError('Invalid submission');
		}

		// Check if the field is the em id
		if($field == 'em_id' && !$this->user->isAdmin()) {
			return $this->ajaxError('You need to be an admin to change the EM');
		}

		// Validate
		$validator = Validator::make([$field => $value], Event::getValidationRules($field), Event::getValidationMessages($field));
		if($validator->fails()) {
			if(!$request->get('field')) {
				$this->throwValidationException($request, $validator);
			} else {
				return $this->ajaxError($validator->messages()->first());
			}
		}

		// Send email if the EM has been set
		if($field == 'em_id' && $value != $event->em_id && $value) {
			$user = User::find($value);
			Mail::queue('emails.events.new_em', [
				'event'    => $event->name,
				'event_id' => $event->id,
				'user'     => $user->forename,
			], function ($message) use ($user) {
				$message->to($user->email, $user->name)
				        ->subject('Volunteered to EM event');
			});
		}

		// Update
		$event->update([
			$field => $value,
		]);
		if(!$request->get('field')) {
			Flash::success('Updated');
		}

		return Response::json(true);
	}

	/**
	 * Update the status of some paperwork.
	 * @param \App\Http\Requests\GenericRequest $request
	 * @param \App\Event                        $event
	 * @return mixed
	 */
	private function update_Paperwork(GenericRequest $request, Event $event)
	{
		// Check the paperwork type is valid
		$type = $request->get('field');
		if(!isset(Event::$Paperwork[$type])) {
			return $this->ajaxError('Unknown paperwork type: ' . $type);
		}

		// Save
		$event->update([
			'paperwork' => array_merge($event->paperwork, [$type => $request->get('value') === 'true']),
		]);

		return Response::json(true);
	}

	/**
	 * Render the diary for a set of given events
	 * @param \Carbon\Carbon $date
	 * @param array          $calendar
	 * @param null           $title
	 * @param null           $redirectUrl
	 * @return mixed
	 */
	private function renderEventsDiary(Carbon $date, array $calendar, $title = null, $redirectUrl = null)
	{
		// Set the previous and next months
		$date->day = 1;
		$date_next = clone $date;
		$date_prev = clone $date;
		$date_next->addMonth();
		$date_prev->subMonth();

		// Calculate the spacings before and after the calendar
		$date->day    = 1;
		$blank_before = ($date->dayOfWeek ?: 7) - 1;
		$date->day    = $date->daysInMonth;
		$blank_after  = 7 - ($date->dayOfWeek ?: 7);

		return View::make('events.diary')->with([
			'date'         => $date,
			'date_next'    => $date_next,
			'date_prev'    => $date_prev,
			'calendar'     => $calendar,
			'blank_before' => $blank_before,
			'blank_after'  => $blank_after,
			'title'        => $title ?: 'Events Diary',
			'redirectUrl'  => $redirectUrl ?: route('events.diary', ['year' => '%year', 'month' => '%month']),
		]);
	}

	/**
	 * Get a list of all the events in a month, sorted by date.
	 * @param \Carbon\Carbon $date
	 * @param \App\User      $member
	 * @return array
	 */
	private function getEventsInMonth(Carbon $date, User $member = null)
	{
		$events = [];
		for($i = 1; $i <= $date->daysInMonth; $i++) {
			$date->day  = $i;
			$events[$i] = Event::onDate($date)
			                   ->whereIn('events.type', ($this->user->isMember() || $this->user->isAdmin()) ? array_keys(Event::$Types) : [Event::TYPE_EVENT])
			                   ->orderBy('event_times.start', 'ASC')
			                   ->forMember($member)
			                   ->get();
		}

		return $events;
	}

	/**
	 * Validate an event time form submission.
	 * @param \App\Http\Requests\GenericRequest $request
	 */
	private function validateEventTime(GenericRequest $request)
	{
		$this->validate($request, [
			'name'       => 'required',
			'date'       => 'required|date_format:d/m/Y',
			'start_time' => 'required|date_format:H:i',
			'end_time'   => 'required|date_format:H:i|after:start_time',
		], [
			'name.required'          => 'Please enter a title for the time',
			'date.required'          => 'Please enter the date',
			'date.date_format'       => 'Please enter a valid date',
			'start_time.required'    => 'Please enter the start time',
			'start_time.date_format' => 'Please enter a valid time',
			'end_time.required'      => 'Please enter the end time',
			'end_time.date_format'   => 'Please enter a valid time',
			'end_time.after'         => 'It cannot end before it\'s begun!',
		]);
	}

	/**
	 * Validate a event crew form submission.
	 * @param \App\Http\Requests\GenericRequest $request
	 * @param bool                              $validateUser
	 */
	private function validateCrew(GenericRequest $request, $validateUser = true)
	{
		$this->validate($request, [
			'user_id' => 'required' . ($validateUser ? '|exists:users,id' : ''),
			'name'    => 'required_if:core,1',
		], [
			'user_id.required' => 'Please select a member',
			'user_id.exists'   => 'Please select a member',
			'name.required_if' => 'Please enter a role title',
		]);
	}

	/**
	 * @param $year
	 * @param $month
	 * @return Carbon
	 */
	private function getDiaryDate($year, $month)
	{
		return $year && $month ? Carbon::create($year, $month, 1) : Carbon::now();
	}

	/**
	 * Allow the finance DB to send an email to the EM.
	 * @param                                   $eventId
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return \Illuminate\Support\Facades\Response
	 */
	public function sendFinanceEmail($eventId, GenericRequest $request)
	{
		// Check the key is valid
		if(env('FINANCE_DB_KEY') == $request->get('appKey')) {
			// Check the event exists
			$event = Event::find($eventId);
			if($event && $event->em_id) {
				$subject = $request->get('subject') . " ({$event->name})";
				Mail::queue('emails.events.finance_db.' . strtolower($request->get('message')), [
					'name'     => $event->em->forename,
					'event'    => $event->name,
					'event_id' => $request->get('fsid'),
				], function ($message) use ($event, $subject) {
					$message->to($event->em->email, $event->em->name)
					        ->subject($subject)
					        ->from('treas@bts-crew.com');
				});

				return Response::json(['code' => 200, 'result' => 'success']);
			} else {
				return Response::json(['code' => '500', 'result' => 'Event not found or no EM assigned'], 500);
			}
		} else {
			return Response::json(['code' => 500, 'result' => 'Incorrect key'], 500);
		}
	}

	/**
	 * Send an email to an event's crew.
	 * @param                                   $id
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return Response
	 */
	public function emailCrew($id, GenericRequest $request)
	{
		// Get the event
		$event = Event::find($id);
		if(!$event) {
			return $this->ajaxError('Couldn\'t find that event', 404);
		}

		// Validate the input
		$this->validate($request, [
			'subject' => 'required',
			'message' => 'required',
		], [
			'subject.required' => 'Please enter the subject',
			'message.required' => 'Please enter the message',
		]);

		// Add the event email
		$event->emails()->create([
			'sender_id' => $this->user->id,
			'header'    => $request->stripped('subject'),
			'body'      => $request->stripped('body'),
		]);

		// Send the email
		$subject = $request->stripped('subject');
		Mail::queue('emails.events.crew_email', [
			'body' => $request->stripped('message'),
			'name' => $event->em ? $event->em->name : '',
		], function ($message) use ($event, $subject) {
			$message->subject($subject);
			if($event->em_id) {
				$message->from($event->em->email, $event->em->name);
			}
			foreach($event->crew as $crew) {
				$message->to($crew->user->email, $crew->user->name);
			}
		});

		Flash::success('Email sent');

		return Response::json(true);
	}
}