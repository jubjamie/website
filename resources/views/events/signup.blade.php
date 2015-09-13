@extends('app')

@section('title', 'Event Signup')

@section('stylesheets')
    @include('partials.tags.style', ['path' => 'partials/events'])
@endsection

@section('content')
    <h1 class="page-header">Event Signup</h1>
    <div id="eventSignup">
        <div class="tabpanel" id="signup">
            {!! $menu !!}
            <div class="tab-content">
                <div class="tab-pane active">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="event-tag hidden-xs"></th>
                                <th class="name">Event</th>
                                <th class="venue hidden-xs hidden-sm">Venue</th>
                                <th class="dates">Dates</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($list) > 0)
                                @foreach($list as $event)
                                    <tr>
                                        <td class="event-tag hidden-xs"><span class="event-entry tag {{ $event->type_class }}">{{ $event->type_string }}</span></td>
                                        <td class="name">
                                            <div>{!! link_to_route('events.view', $event->name, $event->id) !!}</div>
                                            <div class="visible-xs"><span class="event-entry tag {{ $event->type_class }}">{{ $event->type_string }}</span></div>
                                        </td>
                                        <td class="venue hidden-xs hidden-sm">{{ $event->venue }}</td>
                                        <td class="dates"><span>{{ $event->start_date }}</span> <span>&mdash;</span> <span>{{ $event->end_date }}</span></td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4">No events currently require {{ $em ? 'event managers' : 'crew' }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    @include('partials.app.pagination', ['paginator' => $list])
                </div>
                <div class="tab-pane">
                </div>
            </div>
        </div>
    </div>
@endsection