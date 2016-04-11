@extends('app')
@section('page-section', 'events')
@section('title', 'Event Details')

@section('scripts')
    $modal.on('show.bs.modal', function(event) {
        var btn = $(event.relatedTarget);
        if(btn.data('formAction')) {
            var form = $modal.find('form');
            var submitBtn = form.find('#submitTimeModal, #submitCrewModal');
            submitBtn.data('formAction', btn.data('formAction'));
            var action = btn.data('formAction').substr(btn.data('formAction').lastIndexOf('/') + 1);

            if(action == 'update-time') {
                submitBtn.children('span').eq(1).text('Save');
            } else if(action == 'add-time') {
                submitBtn.children('span').eq(1).text('Add Time');
                form.find('#deleteTime').remove();
            } else if(action == 'add-crew') {
                submitBtn.children('span').eq(1).text('Add Crew');
                form.find('select[name="user_id"]').parent().show();
                form.find('p#existingCrewUser').remove();
                form.find('#deleteCrew').remove();
            } else if(action == 'update-crew') {
                submitBtn.children('span').eq(1).text('Save');
                form.find('select[name="user_id"]').parent().hide();
                form.find('p#existingCrewUser').text(btn.data('formData')['user']).show();
                form.find('input[name="core"]').trigger('change');
                form.find('input[name="em"]').trigger('change');
            }
        }
    });
    $modal.on('change', 'input[type="checkbox"][name="core"]', function() {
        var disabled = $(this).prop('checked') ? '' : 'disabled';
        $modal.find('input[name="name"]').prop('disabled', disabled);
        $modal.find('input[name="em"]').prop('disabled', disabled);
        if(disabled == 'disabled') {
            $modal.find('input[name="em"]').prop('checked', false);
            $modal.find('input[name="name"]').val('');
        }
    });
    @if($event->isTraining())
    $modal.on('change', 'input[type="checkbox"][name="em"]', function() {
        if(!$(this).prop('checked')) {
            $('#crewRoleConfirmed').show();
        } else {
            $('#crewRoleConfirmed').hide();
        }
    });
    @endif
    var eventEmailLinks = {
        'all' : $('#emailCrewAll').attr('href'),
        'core' : $('#emailCrewCore').attr('href'),
    };
    function setEmailLinkSubject(subject) {
        $('#emailCrewAll').attr('href', eventEmailLinks['all'] + '?subject=[Backstage Website] ' + subject);
        $('#emailCrewCore').attr('href', eventEmailLinks['core'] + '?subject=[Backstage Website] ' + subject);
    }
    setEmailLinkSubject("{{ $event->name }}");
    $modal.on('keyup', 'input[name="subject"]', function() {
        setEmailLinkSubject($(this).val());
    });
@endsection

@section('content')
    <h1>
        @if($canEdit)
            <span data-editable="true"
                  data-toggle="modal"
                  data-target="#modal"
                  data-modal-class="modal-sm"
                  data-modal-title="Event Name"
                  data-modal-template="event_name"
                  data-form-data="{{ json_encode(['name' => $event->name ]) }}"
                  role="button">{{ $event->name }}</span>
        @else
            {{ $event->name }}
        @endif
    </h1>
    <div id="viewEvent">
        {!! Form::open(['class' => 'form-horizontal']) !!}
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <h2>Event Info</h2>
                    {{-- EM --}}
                    <div class="form-group">
                        {!! Form::label('em', 'Event Manager:', ['class' => 'col-md-5 control-label']) !!}
                        <div class="col-md-7">
                            @if($isAdmin)
                            <p class="form-control-static"
                               data-editable="true"
                               data-edit-type="select"
                               data-edit-source="em"
                               data-control-name="em_id"
                               data-edit-url="{{ route('events.update', ['id' => $event->id, 'action' => 'update-details']) }}"
                               data-value="{{ $event->em_id }}"
                               role="button">{!! $event->em ? $event->em->name : '<em>&ndash; not yet assigned &ndash;</em>' !!}</p>
                            @else
                                <p class="form-control-static">{!! $event->em ? $event->em->name : '<em>&ndash; not yet assigned &ndash;</em>' !!}</p>
                            @endif
                        </div>
                    </div>
                    {{-- Type --}}
                    <div class="form-group">
                        {!! Form::label('type', 'Type:', ['class' => 'col-md-5 control-label']) !!}
                        <div class="col-md-7">
                            @if($canEdit)
                                <p class="form-control-static"
                                   data-editable="true"
                                   data-edit-type="select"
                                   data-edit-source="type"
                                   data-control-name="type"
                                   data-edit-url="{{ route('events.update', ['id' => $event->id, 'action' => 'update-details']) }}"
                                   data-value="{{ $event->type }}"
                                   data-text-format="type"
                                   role="button"><span class="event-entry tag upper {{ $event->type_class }}">{{ $event->type_string }}</span></p>
                            @else
                                <p class="form-control-static"><span class="event-entry tag upper {{ $event->type_class }}">{{ $event->type_string }}</span></p>
                            @endif
                        </div>
                    </div>
                    {{-- Client --}}
                    @if($event->isEvent())
                    <div class="form-group">
                        {!! Form::label('client', 'Client:', ['class' => 'col-md-5 control-label']) !!}
                        <div class="col-md-7">
                            @if($canEdit)
                                <p class="form-control-static"
                                   data-editable="true"
                                   data-edit-type="select"
                                   data-edit-source="client_type"
                                   data-control-name="client_type"
                                   data-edit-url="{{ route('events.update', ['id' => $event->id, 'action' => 'update-details']) }}"
                                   data-value="{{ $event->client_type }}"
                                   role="button">{{ $event->client }}</p>
                            @else
                                <p class="form-control-static">{{ $event->client }}</p>
                            @endif
                        </div>
                    </div>
                    @endif
                    {{-- Venue --}}
                    <div class="form-group">
                        {!! Form::label('venue', 'Venue:', ['class' => 'col-md-5 control-label']) !!}
                        <div class="col-md-7">
                            @if($canEdit)
                                <p class="form-control-static"
                                   data-editable="true"
                                   data-edit-type="text"
                                   data-control-name="venue"
                                   data-edit-url="{{ route('events.update', ['id' => $event->id, 'action' => 'update-details']) }}"
                                   role="button">{{ $event->venue }}</p>
                            @else
                                <p class="form-control-static">{{ $event->venue }}</p>
                            @endif
                        </div>
                    </div>
                    {{-- Description --}}
                    @if($isMember || $canEdit)
                    <div class="form-group">
                        {!! Form::label('description', 'Description:', ['class' => 'col-md-5 control-label']) !!}
                        <div class="col-md-7" style="max-height:200px;overflow-y:auto;">
                            @if($canEdit)
                                <p class="form-control-static"
                                   data-editable="true"
                                   data-edit-type="textarea"
                                   data-edit-url="{{ route('events.update', ['id' => $event->id, 'action' => 'update-details']) }}"
                                   data-control-name="description"
                                   role="button">{!! nl2br($event->description) !!}</p>
                            @else
                                <p class="form-control-static">{!! nl2br($event->description) !!}</p>
                            @endif
                        </div>
                    </div>
                    @endif
                    {{-- Public Description --}}
                    @if($event->isEvent() && (($event->public_description && !$isMember) || $canEdit))
                        <div class="form-group">
                            {!! Form::label('description', ($isMember || $canEdit) ? "Public Description:" : 'Description:', ['class' => 'col-md-5 control-label']) !!}
                            <div class="col-md-7" style="max-height:200px;overflow-y:auto;">
                                @if($canEdit)
                                    <p class="form-control-static"
                                       data-editable="true"
                                       data-edit-type="textarea"
                                       data-edit-url="{{ route('events.update', ['id' => $event->id, 'action' => 'update-details']) }}"
                                       data-control-name="description_public"
                                       data-config="{{ json_encode(['text' => ['' => '- no public description -']]) }}"
                                       data-value="{{ $event->description_public }}"
                                       role="button">{!! nl2br($event->description_public) ?: '- no public description -' !!}</p>
                                @else
                                    <p class="form-control-static">{!! nl2br($event->description_public) !!}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                    {{-- Paperwork --}}
                    @if($event->isEvent() && $canEdit)
                        <h2>Paperwork</h2>
                        @foreach(\App\Event::$Paperwork as $key => $name)
                            <div class="form-group">
                                {!! Form::label('', $name . ':', ['class' => 'col-md-5 control-label']) !!}
                                <div class="col-md-7">
                                    <p class="form-control-static"
                                            data-editable="true"
                                            data-edit-type="toggle"
                                            data-control-name="{{ $key }}"
                                            data-value="{{ !!$event->paperwork[$key] }}"
                                            data-toggle-template="paperwork"
                                            data-edit-url="{{ route('events.update', ['id' => $event->id, 'action' => 'paperwork']) }}"
                                            role="button">
                                        @if($event->paperwork[$key])
                                            @include('events.partials.view_paperwork_complete')
                                        @else
                                            @include('events.partials.view_paperwork_incomplete')
                                        @endif
                                    </p>
                                    @if($key == "risk_assessment")
                                        <p class="paperwork-link">
                                            <span class="fa fa-link"></span>
                                            <a class="grey" href="https://docs.google.com/forms/d/1t355cXGFiw0LtwD2IIEFPGjEwFRzV0wQxh7uUzH39iI/viewform" target="_blank">Risk assessment form</a>
                                        </p>
                                    @elseif($key == "event_report")
                                        <p class="paperwork-link">
                                            <span class="fa fa-link"></span>
                                            <a class="grey" href="https://docs.google.com/forms/d/1KdSfZYT5pOOVPgq_pRqpGtCoY-OWbvpuNzfGPRqnsx4/viewform" target="_blank">Event report form</a>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="col-md-6">
                    @if(($event->crew_list_status > -1 && $isMember) || ($isAdmin || $isEM))
                        {{-- Crew list --}}
                        @include('events.partials.view_crew_list')
                    @else
                        {{-- Event times if can't view crew list --}}
                        @include('events.partials.view_event_times')
                    @endif
                </div>
            </div>
            @if(($event->crew_list_status > -1 && $isMember) || ($isAdmin || $isEM))
            {{-- Event times --}}
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    @include('events.partials.view_event_times')
                </div>
                <div class="col-md-3"></div>
            </div>
            @endif
        </div>
        {!! Form::close() !!}
    </div>
@endsection

@section('modal')
    @if($canEdit)
        <div data-type="modal-template" data-id="event_time">
            @include('events.modal.view_time')
        </div>
        <div data-type="modal-template" data-id="event_crew">
            @include('events.modal.view_crew')
        </div>
        @if($event->isSocial())
        <div data-type="modal-template" data-id="event_crew_guest">
            {!! Form::open() !!}
            <div class="modal-body">
                {{-- Name --}}
                <div class="form-group">
                    {!! Form::label('guest_name', 'Name:', ['class' => 'control-label']) !!}
                    {!! Form::text('guest_name', null, ['class' => 'form-control']) !!}
                </div>
                {{-- Paid --}}
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('confirmed', 1, null) !!}
                            This member has paid
                        </label>
                    </div>
                </div>
                {{-- Crew id --}}
                {!! Form::input('hidden', 'id', null) !!}
                {!! Form::input('hidden', 'guest', true) !!}
            </div>
            <div class="modal-footer">
                <div class="btn-group">
                    <button class="btn btn-success"
                            data-type="submit-modal"
                            id="submitCrewModal"
                            type="button">
                        <span class="fa fa-check"></span>
                        <span>Save</span>
                    </button>
                    <button class="btn btn-danger"
                            data-type="submit-modal"
                            data-submit-confirm="Are you sure you want to delete this crew role?"
                            data-form-action="{{ route('events.update', ['id' => $event->id, 'action' => 'delete-crew']) }}"
                            id="deleteCrew"
                            type="button">
                        <span class="fa fa-remove"></span>
                        <span>Delete</span>
                    </button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        @endif
        <div data-type="modal-template" data-id="event_emails">
            <div class="modal-body">
                {!! Form::open(['class' => 'form-horizontal']) !!}
                    {{-- Subject --}}
                    <div class="form-group">
                        {!! Form::label('subject', 'Subject:', ['class' => 'control-label col-xs-3']) !!}
                        <div class="col-xs-9">
                            {!! Form::text('subject', $event->name, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    {{-- Buttons --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-9">
                            <div>
                                <a class="btn btn-success"
                                   href="mailto:{!! $event->crew->map(function($crew) { return $crew->user->email; })->implode(',') !!}"
                                   id="emailCrewAll">
                                    <span class="fa fa-users"></span>
                                    <span>Send to all crew</span>
                                </a>
                            </div>
                            <div>
                                <a class="btn btn-success"
                                   href="mailto:{!! $event->crew->filter(function($crew) { return $crew->name !== null; })->map(function($crew) { return $crew->user->email; })->implode(',') !!}"
                                   id="emailCrewCore">
                                    <span class="fa fa-user"></span>
                                    <span>Send to core crew</span>
                                </a>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div data-type="modal-template" data-id="event_name">
            {!! Form::open() !!}
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::text('name', null , ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success"
                        data-type="submit-modal"
                        data-form-action="{{ route('events.update', ['id' => $event->id, 'action' => 'update-details']) }}"
                        type="button">
                    <span class="fa fa-check"></span>
                    <span>Save</span>
                </button>
            </div>
            {!! Form::close() !!}
        </div>
        <div data-type="modal-template" data-id="crew_list_status">
            {!! Form::open() !!}
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::select('crew_list_status', [-1 => 'Hidden', 0 => 'Closed', 1 => 'Open'], null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success"
                        data-type="submit-modal"
                        data-form-action="{{ route('events.update', ['id' => $event->id, 'action' => 'update-details']) }}"
                        type="button">
                    <span class="fa fa-check"></span>
                    <span>Save</span>
                </button>
            </div>
            {!! Form::close() !!}
        </div>
        @if($event->isEvent())
        <div data-type="data-toggle-template" data-toggle-id="paperwork" data-value="false">
            @include('events.partials.view_paperwork_incomplete')
        </div>
        <div data-type="data-toggle-template" data-toggle-id="paperwork" data-value="true">
            @include('events.partials.view_paperwork_complete')
        </div>
        <div data-type="data-select-source" data-select-name="client_type">
            {!! Form::select('client_type', App\Event::$Clients, null, ['class' => 'form-control']) !!}
        </div>
        @endif
        <div data-type="data-select-source" data-select-name="em" data-config="{{ json_encode(['text' => ['' => '- not yet assigned -'] + array_map(function($value) { return substr($value, 0, stripos($value, '(') - 1);}, $users_em)]) }}">
            {!! Form::select('em_id', ['' => '- not yet assigned -'] + $users_em, null, ['class' => 'form-control']) !!}
        </div>
        <div data-type="data-select-source" data-select-name="type">
            {!! Form::select('type', App\Event::$Types, null, ['class' => 'form-control']) !!}
        </div>
        <div data-type="data-text-format" data-name="type" data-config="{{ json_encode(['class' => \App\Event::$TypeClasses]) }}">
            <span class="event-entry tag upper #class">#text</span>
        </div>
    @endif
@endsection