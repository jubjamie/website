@extends('app')

@section('title', $title)

@section('stylesheets')
    @include('partials.tags.style', ['path' => 'partials/events'])
@endsection

@section('javascripts')
    @include('partials.tags.script', ['path' => 'partials/diary'])
@endsection

@section('styles')
    @media (min-width: 992px) {
        #diary div.diary div.calendar div.cell:nth-of-type(7n+{{ (7 - $blank_before) }}) {
            border-right: 1px solid #444;
        }
        #diary div.diary div.calendar div.cell:nth-last-of-type(-n+{{ (7 - $blank_after) }}) {
            border-bottom: 1px solid #444;
        }
    }
@endsection

@section('content')
    <h1 class="page-header">@yield('title')</h1>
    <div id="diary">
        <div class="date-header">
            <a class="prev" href="{{ str_replace(['%year', '%month'], [$date_prev->year, $date_prev->month], $redirectUrl) }}">
                <span class="fa fa-caret-left"></span>
            </a>
            <span class="month"
                  data-toggle="modal"
                  data-target="#modal"
                  data-modal-template="diary_date"
                  data-modal-class="modal-sm"
                  data-modal-title="Change Date"
                  title="Select month and year"
                  role="button">{{ $date->format('F Y') }}</span>
            <a class="next" href="{{ str_replace(['%year', '%month'], [$date_next->year, $date_next->month], $redirectUrl) }}">
                <span class="fa fa-caret-right"></span>
            </a>
        </div>
        <div class="diary">
            <div class="day-headers">
                <div class="cell">Mon</div>
                <div class="cell">Tue</div>
                <div class="cell">Wed</div>
                <div class="cell">Thu</div>
                <div class="cell">Fri</div>
                <div class="cell">Sat</div>
                <div class="cell">Sun</div>
            </div>
            <div class="calendar">
                @if($blank_before > 0)
                    <span class="cell blank" style="width: {{ $blank_before * 100 / 7 }}%"></span>
                @endif
                @for($i = 1; $i <= $date->daysInMonth; $i++)
                    <div class="cell day">
                        <span class="date">{{ $i }}</span>
                        @if(isset($calendar[$i]) && count($calendar[$i]) > 0)
                            <ul class="event-list">
                                @foreach($calendar[$i] as $event)
                                    <li class="event-entry {{ $event->type_class }}">
                                        <a href="{{ route('events.view', $event->id) }}">{{ $event->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endfor
                @if($blank_after > 0)
                    <span class="cell blank" style="width: {{ $blank_after * 100 / 7 }}%"></span>
                @endif
            </div>
        </div>
    </div>
    @if(Auth::check() && Auth::user()->isMember())
        <div class="event-key">
            <h1>Key</h1>
            <ul class="event-list">
                @foreach(\App\Event::$Types as $i => $type)
                    <li class="event-entry {{ \App\Event::$TypeClasses[$i] }}"><span>{{ $type }}</span></li>
                @endforeach
            </ul>
        </div>
        <p>
            <a class="btn btn-primary" data-toggle="modal" data-target="#modal" data-modal-class="modal-md" data-modal-template="google_calendar" href="#">
                <span class="fa fa-google"></span>
                <span>Add to Google Calendar</span>
            </a>
            @if(Auth::user()->isAdmin())
                <a class="btn btn-success" href="{{ route('events.add') }}">
                    <span class="fa fa-plus"></span>
                    <span>Add an event to the diary</span>
                </a>
            @endif
        </p>
    @endif
@endsection

@section('modal')
    <div data-type="modal-template" data-id="diary_date">
        @include('events.modal.diary_date')
    </div>
    @if($activeUser->isMember())
    <div data-type="modal-template" data-id="google_calendar">
        <div class="modal-header"><h1>Add to Google Calendar</h1></div>
        <div class="modal-body">
            <p>To add the events diary to your Google Calendar:</p>
            <ol>
                <li>Go to <a href="http://calendar.google.com/" target="_blank">Google Calendar</a></li>
                <li>Go to the <strong>Other Calendars</strong> menu on the left-hand side, click the down arrow and choose <strong>Add by URL</strong>.</li>
                <li>Enter this URL:</li>
                <kbd style="font-size:12px;">
                    {{ route('events.export') }}
                </kbd>
                <li>Customise the calendar with a name, notifications, etc., as you would any other calendar.</li>
            </ol>
            <h4>Please note</h4>
            <ul>
                <li>We currently cannot guarantee how quickly the events diary in Google Calendar will update. This depends on how often Google requests for
                    updates, which can't be configured.</li>
                <li>This currently only contains events - training, socials and meetings may be added in the future.</li>
            </ul>
        </div>
        <div class="modal-footer">
            <button class="btn btn-success" data-toggle="modal" data-target="#modal" type="button">
                <span class="fa fa-thumbs-up"></span>
                <span>Ok, got it</span>
            </button>
        </div>
    </div>
    <div data-type="modal-template" data-id="date_gantt">
        <div class="modal-header">
            <h1>Monday 21st September 2015</h1>
        </div>
    </div>
    @endif
@endsection