@extends('app')

@section('title', 'Event List')

@section('stylesheets')
    @include('partials.tags.style', ['path' => 'partials/events'])
@endsection

@section('content')
    <h1 class="page-header">@yield('title')</h1>
    <div id="eventList">
        <a class="btn btn-success" href="{{ route('events.add') }}">
            <span class="fa fa-plus"></span>
            <span>Add an event to the diary</span>
        </a>
        <table class="table table-striped">
            <thead>
                <th class="event">Event</th>
                <th class="venue">Venue</th>
                <th class="crew">Crew</th>
                <th class="date">Date</th>
                <th class="paperwork">Paperwork</th>
                <th class="buttons"></th>
            </thead>
            <tbody>
                @if(count($events) > 0)
                    @foreach($events as $event)
                        <tr>
                            <td class="event">
                                <div>
                                    {!! link_to_route('events.view', $event->name, $event->id) !!}
                                </div>
                                <div>
                                    <span class="event-entry tag {{ $event->type_class }}">{{ $event->type_string }}</span>
                                </div>
                            </td>
                            <td class="venue">{{ $event->venue }}</td>
                            <td class="crew">
                                @if($event->hasEM())
                                    {{ $event->em['forename'] }} {{ $event->em['surname'] }}<br>
                                @else
                                    <em>&ndash; no em &ndash;</em>
                                @endif
                                <ul class="crew-list">
                                    @foreach($event->crew as $crew)
                                        <li>&mdash; {{ $crew->user['forename'] . ' ' . $crew->user['surname'] }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="date">{{ $event->start_date }}<br>to<br>{{ $event->end_date }}</td>
                            <td class="paperwork">
                                <ul>
                                    @foreach(\App\Event::$Paperwork as $key => $name)
                                        <li><span class="fa {{ $event->paperwork[$key] ? 'fa-check success' : 'fa-remove danger' }}"></span> {{ $name }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="buttons text-center">
                                <button class="btn btn-danger btn-sm"
                                        data-submit-ajax="{{ route('events.delete', $event->id) }}"
                                        data-submit-confirm="Are you sure you want to delete this event?"
                                        data-success-url="{{ route('events.index') }}"
                                        type="button"
                                        title="Delete this event">
                                    <span class="fa fa-remove"></span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4">We don't have any events :/</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    @include('partials.app.pagination', ['paginator' => $events])
@endsection