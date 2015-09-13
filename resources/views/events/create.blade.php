@extends('app')

@section('title', 'Add an Event')

@section('scripts')
    $('input[name="one_day"]').on('change', function() {
        $('#eventDates').find('.date-hide').css('display', $(this).prop('checked') ? 'none' : 'block');
    });
    $('input[name="one_day"]').trigger('change');
@endsection

@section('content')
    <h1 class="page-header">Add an Event</h1>
    <div id="createEvent">
        {!! Form::model(new \App\Event(), ['class' => 'form-horizontal', 'style' => 'max-width: 550px']) !!}
            {{-- Event name --}}
            <div class="form-group @include('partials.form.error-class', ['name' => 'name'])">
                {!! Form::label('name', 'Event Name', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'What is the event called?']) !!}
                    @include('partials.form.input-error', ['name' => 'name'])
                </div>
            </div>

            {{-- Event manager --}}
            <div class="form-group @include('partials.form.error-class', ['name' => 'em_id'])">
                {!! Form::label('em_id', 'Event Manager', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    {!! Form::select('em_id', [null => '-- No EM --'] + $users, null, ['class' => 'form-control']) !!}
                    @include('partials.form.input-error', ['name' => 'em_id'])
                </div>
            </div>

            {{-- Event type --}}
            <div class="form-group @include('partials.form.error-class', ['name' => 'type'])">
                {!! Form::label('type', 'Event Type', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    {!! Form::select('type', \App\Event::$Types, null, ['class' => 'form-control']) !!}
                    @include('partials.form.input-error', ['name' => 'type'])
                </div>
            </div>

            {{-- Description --}}
            <div class="form-group @include('partials.form.error-class', ['name' => 'description'])">
                {!! Form::label('description', 'Description', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Briefly describe what the event is about']) !!}
                    @include('partials.form.input-error', ['name' => 'description'])
                </div>
            </div>
            <div class="form-group" style="margin-bottom:25px;margin-top:-10px;">
                <div class="col-md-3"></div>
                <div class="col-md-9">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('desc_public', 1, null) !!}
                            Make this visible to the public too
                        </label>
                    </div>
                </div>
            </div>

            {{-- Venue --}}
            <div class="form-group @include('partials.form.error-class', ['name' => 'venue'])">
                {!! Form::label('venue', 'Venue', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    {!! Form::text('venue', null, ['class' => 'form-control', 'placeholder' => 'Where is it?']) !!}
                    @include('partials.form.input-error', ['name' => 'venue'])
                </div>
            </div>

            {{-- Venue type --}}
            <div class="form-group @include('partials.form.error-class', ['name' => 'venue_type'])">
                {!! Form::label('venue_type', 'Venue Type', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    {!! Form::select('venue_type', \App\Event::$VenueTypes, null, ['class' => 'form-control']) !!}
                    @include('partials.form.input-error', ['name' => 'venue_type'])
                </div>
            </div>

            {{-- Client type --}}
            <div class="form-group @include('partials.form.error-class', ['name' => 'client_type'])">
                {!! Form::label('client_type', 'Client Type', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    {!! Form::select('client_type', \App\Event::$Clients, null, ['class' => 'form-control']) !!}
                    @include('partials.form.input-error', ['name' => 'client_type'])
                </div>
            </div>

            {{-- Dates --}}
            <div class="form-group @include('partials.form.error-class', ['name' => 'date_start']) @include('partials.form.error-class', ['name' => 'date_end'])" id="eventDates">
                {!! Form::label('date_start', 'Date:', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    <div class="form-group">
                        <div class="col-xs-5">
                            {!! Form::text('date_start', null, ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy']) !!}
                        </div>
                        <div class="col-xs-2 date-hide">
                            <p class="form-control-static text-center">to</p>
                        </div>
                        <div class="col-xs-5 date-hide">
                            {!! Form::text('date_end', null, ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy']) !!}
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:-15px;">
                        <div class="col-xs-5">
                            @include('partials.form.input-error', ['name' => 'date_start'])
                        </div>
                        <div class="col-xs-2 date-hide"></div>
                        <div class="col-xs-5 date-hide">
                            @include('partials.form.input-error', ['name' => 'date_end'])
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('one_day', 1, null) !!}
                                    This is a one-day event
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Times --}}
            <div class="form-group @include('partials.form.error-class', ['name' => 'time_start']) @include('partials.form.error-class', ['name' => 'time_end'])">
                {!! Form::label('time_start', 'Time:', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    <div class="form-group">
                        <div class="col-xs-5">
                            {!! Form::text('time_start', '19:00', ['class' => 'form-control', 'placeholder' => 'hh:mm']) !!}
                        </div>
                        <div class="col-xs-2">
                            <p class="form-control-static text-center">to</p>
                        </div>
                        <div class="col-xs-5">
                            {!! Form::text('time_end', '22:30', ['class' => 'form-control', 'placeholder' => 'hh:mm']) !!}
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:-15px;">
                        <div class="col-xs-5">
                            @include('partials.form.input-error', ['name' => 'time_start'])
                        </div>
                        <div class="col-xs-2"></div>
                        <div class="col-xs-5">
                            @include('partials.form.input-error', ['name' => 'time_end'])
                        </div>
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="form-group">
                <div class="col-md-3"></div>
                <div class="col-md-9">
                    <button class="btn btn-success" disable-submit="Adding event ...">
                        <span class="fa fa-check"></span>
                        <span>Add event</span>
                    </button>
                    <a class="btn btn-danger" href="{{ route('events.diary') }}">
                        <span class="fa fa-undo"></span>
                        <span>Cancel</span>
                    </a>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
@endsection