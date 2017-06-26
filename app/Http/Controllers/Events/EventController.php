<?php

namespace App\Http\Controllers\Events;

use App\Event;
use App\Http\Controllers\Controller;
use App\Http\Requests\Events\EventRequest;
use App\Mail\Events\AcceptedExternal;
use App\Traits\CorrectsTimezone;
use bnjns\SearchTools\SearchTools;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Szykra\Notifications\Flash;

class EventController extends Controller
{
    use CorrectsTimezone;
    
    /**
     * Set the basic authentication requirements.
     */
    public function __construct()
    {
        $this->middleware('auth')
             ->except(['view']);
    }
    
    /**
     * View a list of events.
     * @param \bnjns\SearchTools\SearchTools $searchTools
     * @return $this
     */
    public function index(SearchTools $searchTools)
    {
        $this->authorize('index', Event::class);
        
        // Start the query
        $events = Event::newestFirst();
        
        // Add the search requirement
        $search = $searchTools->search();
        if(!is_null($search) && $search) {
            $events = $events->where(function ($query) use ($search) {
                $query->where('events.name', 'LIKE', '%' . $search . '%')
                      ->orWhere('events.venue', 'LIKE', '%' . $search . '%');
            });
        }
        
        // Paginate the results
        $events = $events->distinctPaginate(20);
        $this->checkPagination($events);
        
        return view('events.index')->with('events', $events);
    }
    
    /**
     * View the form to create an event.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create', Event::class);
        return view('events.create');
    }
    
    /**
     * Process the form and add the event to the database.
     * @param \App\Http\Requests\Events\EventRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(EventRequest $request)
    {
        // Set the data
        $data = [
            'name'               => $request->get('name'),
            'venue'              => $request->get('venue'),
            'description'        => $request->get('description'),
            'description_public' => $request->get('description'),
            'type'               => $request->get('type'),
            'client_type'        => $request->get('type') == Event::TYPE_EVENT ? $request->get('client_type') : null,
            'venue_type'         => $request->get('type') == Event::TYPE_EVENT ? $request->get('venue_type') : null,
            'crew_list_status'   => 1,
            'paperwork'          => [
                'risk_assessment' => false,
                'insurance'       => false,
                'finance_em'      => false,
                'finance_treas'   => false,
                'event_report'    => false,
            ],
        ];
        if($request->get('em_id')) {
            $data['em_id'] = $request->get('em_id');
        }
        
        // Create the event
        $event = Event::create(clean($data));
        
        // Set the event time limits
        $start_time = explode(':', $request->get('time_start'));
        $end_time   = explode(':', $request->get('time_end'));
        $date       = Carbon::createFromFormat('Y-m-d', $request->get('date_start'))
                            ->setTime(0, 0, 0);
        $date_end   = Carbon::createFromFormat('Y-m-d', $request->has('one_day') ? $request->get('date_start') : $request->get('date_end'))
                            ->setTime(23, 59, 59);
        
        // Create each event time
        while($date->lte($date_end)) {
            $event->times()->create([
                'name'  => $event->name,
                'start' => $this->correctTimezone($date->copy()->setTime($start_time[0], $start_time[1]), $request),
                'end'   => $this->correctTimezone($date->copy()->setTime($end_time[0], $end_time[1]), $request),
            ]);
            $date->day++;
        }
        
        // Add to the finance database
        $event->addToFinanceDb();
        
        // If the event is external and off-campus email Alison
        if($event->client_type > 1 && $event->venue_type == 2) {
            Mail::to('a.j.fleet@bath.ac.uk')
                ->queue(new AcceptedExternal($event, $request));
        }
        
        
        // Create a flash message and redirect
        Flash::success('Event created');
        
        if($request->get('action') == 'create-another') {
            return redirect()->back();
        } else {
            return redirect()->route('event.view', ['id' => $event->id]);
        }
    }
    
    /**
     * View an event's details.
     * @param                          $eventId
     * @param \Illuminate\Http\Request $request
     * @return $this
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function view($eventId, Request $request)
    {
        // Get the event
        $event = Event::findOrFail($eventId);
        
        // Check the user can view the event
        if($event->type !== Event::TYPE_EVENT && (!Auth::check() || !$request->user()->isMember())) {
            throw new AuthorizationException();
        }
        
        return view('events.view')->with([
            'event' => $event,
            'tab'   => $request->has('tab') ? $request->get('tab') : 'details',
        ]);
    }
    
    /**
     * Update the event.
     * @param                          $eventId
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Controllers\Events\EventController|\Illuminate\Http\RedirectResponse
     */
    public function update($eventId, Request $request)
    {
        $event = Event::findOrFail($eventId);
        $this->authorize('update', $event);
        
        $action = $request->get('action');
        if($action == 'update') {
            return $this->updateDetails($event, $request);
        } else if(preg_match('/^clear-crew:(.*)$/', $action, $matches)) {
            return $this->updateClearCrew($event, $matches[1]);
        } else if($action == 'update-field' && $request->ajax() && preg_match('/^paperwork.(.*)$/', $request->get('field'), $matches)) {
            return $this->updatePaperwork($event, $matches[1], $request->get('value'));
        } else {
            return redirect()->route('event.view', ['id' => $eventId, 'tab' => 'settings']);
        }
    }
    
    /**
     * Clear the crew list
     * @param \App\Event $event
     * @param            $mode
     * @return \Illuminate\Http\RedirectResponse
     */
    private function updateClearCrew(Event $event, $mode)
    {
        if($mode == 'all') {
            $event->crew()
                  ->delete();
            Flash::success('Crew list cleared');
        } else if($mode == 'core') {
            $event->crew()
                  ->core()
                  ->delete();
            Flash::success('Core crew cleared');
        } else if($mode == 'guests' && $event->isSocial()) {
            $event->crew()
                  ->guest()
                  ->delete();
            Flash::success('Guests cleared');
        }
        
        return redirect()->route('event.view', ['id' => $event->id, 'tab' => 'crew']);
    }
    
    /**
     * Update the event's details.
     * @param \App\Event               $event
     * @param \Illuminate\Http\Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    private function updateDetails(Event $event, Request $request)
    {
        // Determine the fields to update
        $fields = [
            'name',
            'type',
            'client_type',
            'venue_type',
            'venue',
            'description',
            'crew_list_status',
        ];
        if(!$event->isTEM($request->user())) {
            $fields[] = 'em_id';
        }
        if($request->user()->can('create', Event::class)) {
            $fields[] = 'client_type';
            $fields[] = 'venue_type';
        }
        
        // Set up the validation
        $rules     = Event::getValidationRules($fields);
        $messages  = Event::getValidationMessages($fields);
        $validator = validator($request->only($fields), $rules, $messages);
        
        // Test validation
        if($validator->fails()) {
            return redirect()->route('event.view', ['id' => $event->id, 'tab' => 'settings'])
                             ->withInput($request->input())
                             ->withErrors($validator);
        }
        
        // Update the event
        $event->update(clean($request->only($fields)));
        
        // If the event is no longer a social, remove any guests
        if($event->type != Event::TYPE_SOCIAL) {
            $event->crew()
                  ->guest()
                  ->delete();
        }
        
        Flash::success('Event updated');
        return redirect()->route('event.view', ['id' => $event->id, 'tab' => 'settings']);
    }
    
    /**
     * Update the event paperwork.
     * @param \App\Event $event
     * @param            $paperwork
     * @param            $value
     * @return \Illuminate\Http\JsonResponse
     */
    private function updatePaperwork(Event $event, $paperwork, $value)
    {
        if(!isset(Event::$Paperwork[$paperwork])) {
            return $this->ajaxError(0, 404, 'Unknown paperwork');
        }
        
        $event->setPaperwork($paperwork, $value);
        return $this->ajaxResponse('Paperwork status updated');
    }
    
    /**
     * Delete an event.
     * @param $eventId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($eventId)
    {
        $this->requireAjax();
        $this->authorize('delete', Event::class);
        
        Event::findOrFail($eventId)
             ->delete();
        
        Flash::success('Event deleted.');
        return $this->ajaxResponse('Event deleted.');
    }
}