<?php

namespace App\Http\Controllers\Events;

use App\Event;
use App\EventTime;
use App\Http\Controllers\Controller;
use App\Traits\CorrectsTimezone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Szykra\Notifications\Flash;

class TimeController extends Controller
{
    use CorrectsTimezone;
    
    /**
     * Set the basic authentication requirements.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Process the form and create the new crew role.
     * @param                          $eventId
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($eventId, Request $request)
    {
        // Authorise
        $this->requireAjax();
        $event = Event::findOrFail($eventId);
        $this->authorize('update', $event);
        
        // Validate
        $fields = ['name', 'start', 'end'];
        $this->validate($request, EventTime::getValidationRules($fields), EventTime::getValidationMessages($fields));
        
        // Create the time
        $event->times()->create([
            'name'  => clean($request->get('name')),
            'start' => $this->correctTimezone(Carbon::createFromFormat('Y-m-d H:i', $request->get('start')), $request),
            'end'   => $this->correctTimezone(Carbon::createFromFormat('Y-m-d H:i', $request->get('end')), $request),
        ]);
        
        Flash::success('Event time created');
        return $this->ajaxResponse('Event time created');
    }
    
    /**
     * Update an event time.
     * @param                          $eventId
     * @param                          $timeId
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($eventId, $timeId, Request $request)
    {
        // Authorise
        $this->requireAjax();
        $event = Event::findOrFail($eventId);
        $time  = $event->times()
                       ->where('id', $timeId)
                       ->firstOrFail();
        $this->authorize('update', $time);
        
        // Validate
        $fields = ['name', 'start', 'end'];
        $this->validate($request, EventTime::getValidationRules($fields), EventTime::getValidationMessages($fields));
        
        // Update
        $time->update([
            'name'  => clean($request->get('name')),
            'start' => $this->correctTimezone(Carbon::createFromFormat('Y-m-d H:i', $request->get('start')), $request),
            'end'   => $this->correctTimezone(Carbon::createFromFormat('Y-m-d H:i', $request->get('end')), $request),
        ]);
        
        Flash::success('Event time updated');
        return $this->ajaxResponse('Event time updated');
    }
    
    /**
     * Delete an event time.
     * @param                          $eventId
     * @param                          $timeId
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($eventId, $timeId, Request $request)
    {
        // Authorise
        $event = Event::findOrFail($eventId);
        $time  = $event->times()
                       ->where('id', $timeId)
                       ->firstOrFail();
        $this->authorize('delete', $time);
    
        // Check that it isn't the last event time
        if($event->times()->count() == 1) {
            return $this->ajaxError(0, 422, 'An event needs at least 1 event time.');
        }
        
        // Delete
        $time->delete();
        Flash::success('Event time deleted');
        return $this->ajaxResponse('Event time deleted');
    }
}