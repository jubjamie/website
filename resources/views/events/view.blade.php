@extends('app')

@section('title', 'Event Details')

@section('stylesheets')
    @include('partials.tags.style', ['path' => 'partials/events'])
@endsection

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
                form.find('#deleteTime').show();
            } else if(action == 'add-time') {
                submitBtn.children('span').eq(1).text('Add Time');
                form.find('#deleteTime').hide();
            } else if(action == 'add-crew') {
                submitBtn.children('span').eq(1).text('Add Crew');
                form.find('select[name="user_id"]').parent().show();
                form.find('p#existingCrewUser').hide();
                form.find('#deleteCrew').hide();
            } else if(action == 'update-crew') {
                submitBtn.children('span').eq(1).text('Save');
                form.find('select[name="user_id"]').parent().hide();
                form.find('p#existingCrewUser').text(btn.data('formData')['user']).show();
                form.find('#deleteCrew').show();
                form.find('input[name="core"]').trigger('change');
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
                        {!! Form::label('em', 'Event Manager:', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-8">
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
                        {!! Form::label('type', 'Type:', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-8">
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
                    <div class="form-group">
                        {!! Form::label('client', 'Client:', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-8">
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
                    {{-- Venue --}}
                    <div class="form-group">
                        {!! Form::label('venue', 'Venue:', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-8">
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
                        {!! Form::label('description', 'Description:', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-8">
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
                    @if(($event->public_description && !$isMember) || $canEdit)
                        <div class="form-group">
                            {!! Form::label('description', ($isMember || $canEdit) ? "Description:(Public)" : 'Description:', ['class' => 'col-md-4 control-label']) !!}
                            <div class="col-md-8">
                                @if($canEdit)
                                    <p class="form-control-static"
                                       data-editable="true"
                                       data-edit-type="textarea"
                                       data-edit-url="{{ route('events.update', ['id' => $event->id, 'action' => 'update-details']) }}"
                                       data-control-name="description_public"
                                       role="button">{!! nl2br($event->description_public) ?: '<em>&ndash; no public description &ndash;</em>' !!}</p>
                                @else
                                    <p class="form-control-static">{!! nl2br($event->description_public) !!}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                    {{-- Paperwork --}}
                    @if($canEdit)
                        <h2>Paperwork</h2>
                        @foreach(\App\Event::$Paperwork as $key => $name)
                            <div class="form-group">
                                {!! Form::label('', $name . ':', ['class' => 'col-md-5 control-label']) !!}
                                <div class="col-md-7">
                                    @if($canEdit)
                                    <p class="form-control-static"
                                            data-editable="true"
                                            data-edit-type="toggle"
                                            data-key="{{ $key }}"
                                            data-value="{{ !!$event->paperwork[$key] }}"
                                            data-toggle-template="paperwork"
                                            data-edit-url="{{ route('events.update', ['id' => $event->id, 'action' => 'paperwork']) }}"
                                            role="button">
                                    @else
                                        <p class="form-control-static">
                                    @endif
                                        @if($event->paperwork[$key])
                                            @include('events.partials.view_paperwork_complete')
                                        @else
                                            @include('events.partials.view_paperwork_incomplete')
                                        @endif
                                    </p>
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
        <div data-type="modal-template" data-id="event_emails">
            {!! Form::open() !!}
            <div class="modal-body">
                {{-- Subject --}}
                <div class="form-group">
                    {!! Form::label('subject', 'Subject:', ['class' => 'control-label']) !!}
                    {!! Form::text('subject', null, ['class' => 'form-control']) !!}
                </div>
                {{-- Message --}}
                <div class="form-group">
                    {!! Form::label('message', 'Message:', ['class' => 'control-label']) !!}
                    {!! Form::textarea('message', null, ['class' => 'form-control', 'rows' => 6]) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" data-type="submit-modal" data-form-action="{{ route('events.email', $event->id) }}" type="button">
                    <span class="fa fa-check"></span>
                    <span>Send email</span>
                </button>
                <a class="btn btn-success" href="{!! $crew_emails !!}">
                    <span class="fa fa-envelope"></span>
                    <span>Use mail client</span>
                </a>
            </div>
            {!! Form::close() !!}
        </div>
        <div data-type="modal-template" data-id="event_name">
            {!! Form::open() !!}
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::text('name', null , ['class' => 'form-control']) !!}
                </div>
                <div class="form-group text-right">
                    <button class="btn btn-success" data-type="submit-modal" data-form-action="{{ route('events.update', ['id' => $event->id, 'action' => 'update-details']) }}" type="button">
                        <span class="fa fa-check"></span>
                        <span>Save</span>
                    </button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        <div data-type="modal-template" data-id="crew_list_status">
            {!! Form::open() !!}
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::select('crew_list_status', [-1 => 'Hidden', 0 => 'Closed', 1 => 'Open'], null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group text-right">
                    <button class="btn btn-success" data-type="submit-modal" data-form-action="{{ route('events.update', ['id' => $event->id, 'action' => 'update-details']) }}" type="button">
                        <span class="fa fa-check"></span>
                        <span>Save</span>
                    </button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        <div data-type="data-toggle-template" data-toggle-id="paperwork" data-value="false">
            @include('events.partials.view_paperwork_incomplete')
        </div>
        <div data-type="data-toggle-template" data-toggle-id="paperwork" data-value="true">
            @include('events.partials.view_paperwork_complete')
        </div>
        <div data-type="data-select-source" data-select-name="em" data-config="{{ json_encode(['text' => ['' => '- not yet assigned -'] + array_map(function($value) { return substr($value, 0, stripos($value, '(') - 1);}, $users_em)]) }}">
            {!! Form::select('em_id', ['' => '- not yet assigned -'] + $users_em, null, ['class' => 'form-control']) !!}
        </div>
        <div data-type="data-select-source" data-select-name="type">
            {!! Form::select('type', App\Event::$Types, null, ['class' => 'form-control']) !!}
        </div>
        <div data-type="data-select-source" data-select-name="client_type">
            {!! Form::select('client_type', App\Event::$Clients, null, ['class' => 'form-control']) !!}
        </div>
        <div data-type="data-text-format" data-name="type" data-config="{{ json_encode(['class' => \App\Event::$TypeClasses]) }}">
            <span class="event-entry tag upper #class">#text</span>
        </div>
    @endif
@endsection