<div id="eventSignup">
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="event">Event</th>
                <th class="role">Role</th>
                <th class="dates hidden-xs">Dates</th>
            </tr>
        </thead>
        <tbody>
            @if(count($events_past) > 0 || count($events_active) > 0)
                @foreach($events_active as $event)
                    <tr>
                        <td class="event">
                            <div>{!! link_to_route('events.view', $event->name, $event->id) !!}</div>
                            <div>
                                <span class="event-entry tag success">active</span>
                                <span class="event-entry tag {{ $event->type_class }}">{{ $event->type_string }}</span>
                            </div>
                        </td>
                        <td class="role">{{ $event->getCrewRole($user) }}</td>
                        <td class="dates hidden-xs"><span>{{ $event->start_date }}</span> <span>&mdash;</span> <span>{{ $event->end_date }}</span></td>
                    </tr>
                @endforeach
                    @foreach($events_past as $event)
                        <tr>
                            <td class="event">
                                <div>{!! link_to_route('events.view', $event->name, $event->id) !!}</div>
                                <div>
                                    <span class="event-entry tag danger">past</span>
                                    <span class="event-entry tag {{ $event->type_class }}">{{ $event->type_string }}</span>
                                </div>
                            </td>
                            <td class="role">{{ $event->getCrewRole($user) }}</td>
                            <td class="dates hidden-xs"><span>{{ $event->start_date }}</span> <span>&mdash;</span> <span>{{ $event->end_date }}</span></td>
                        </tr>
                    @endforeach
            @else
                <tr>
                    <td colspan="3">{{ $user->forename }} doesn't have any events yet</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>