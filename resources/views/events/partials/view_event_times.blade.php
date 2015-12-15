<h2 class="{{ $event->crew_list_status > -1 && ($isMember || $isAdmin) ? 'text-center' : '' }}">Event Times</h2>
<div class="event-times">
    @if(count($event->event_times) > 0)
        @foreach($event->event_times as $date => $times)
            @foreach($times as $i => $time)
                @if($canEdit)
                <div class="event-time"
                     data-editable="true"
                     data-toggle="modal"
                     data-target="#modal"
                     data-modal-template="event_time"
                     data-modal-title="Edit Event Time"
                     data-modal-class="modal-sm"
                     data-form-action="{{ route('events.update', ['id' => $event->id, 'action' => 'update-time']) }}"
                     data-form-data="{{ json_encode([
                     'id' => $time->id,
                     'name' => $time->name,
                     'start_year' => $time->start->tz(env('SERVER_TIMEZONE', 'UTC'))->format('Y'),
                     'start_month' => $time->start->tz(env('SERVER_TIMEZONE', 'UTC'))->format('n'),
                     'start_day' => $time->start->tz(env('SERVER_TIMEZONE', 'UTC'))->format('j'),
                     'start_hour' => $time->start->tz(env('SERVER_TIMEZONE', 'UTC'))->format('G'),
                     'start_minute' => (int)$time->start->tz(env('SERVER_TIMEZONE', 'UTC'))->format('i'),
                     'end_year' => $time->end->tz(env('SERVER_TIMEZONE', 'UTC'))->format('Y'),
                     'end_month' => $time->end->tz(env('SERVER_TIMEZONE', 'UTC'))->format('n'),
                     'end_day' => $time->end->tz(env('SERVER_TIMEZONE', 'UTC'))->format('j'),
                     'end_hour' => $time->end->tz(env('SERVER_TIMEZONE', 'UTC'))->format('G'),
                     'end_minute' => (int)$time->end->tz(env('SERVER_TIMEZONE', 'UTC'))->format('i'),
                     ]) }}"
                     role="button">
                @else
                    <div class="event-time">
                @endif
                    <div class="date">{{ $i == 0 ? $date : '&nbsp;' }}</div>
                    <div class="time">{{ $time->start->tz(env('SERVER_TIMEZONE', 'UTC'))->format('H:i') }} &ndash; {{ $time->end->tz(env('SERVER_TIMEZONE', 'UTC'))->format('H:i') }}</div>
                    <div class="name">{{ $time->name }}</div>
                </div>
            @endforeach
        @endforeach
    @else
        <h4>No times exist for this event.</h4>
    @endif
</div>
@if($canEdit)
    <button class="btn btn-success"
            data-toggle="modal"
            data-mode="new"
            data-target="#modal"
            data-modal-template="event_time"
            data-modal-class="modal-sm"
            data-modal-title="Add Event Time"
            data-form-action="{{ route('events.update', ['id' => $event->id, 'action' => 'add-time']) }}"
            type="button">
        <span class="fa fa-plus"></span>
        <span>Add event time</span>
    </button>
@endif