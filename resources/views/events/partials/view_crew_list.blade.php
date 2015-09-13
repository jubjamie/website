<h2>Crew List
    @if($canEdit)
        <span class="crew-status"
              data-editable="true"
              data-toggle="modal"
              data-target="#modal"
              data-modal-class="modal-sm"
              data-modal-title="Crew List Status"
              data-modal-template="crew_list_status"
              data-form-data="{{ json_encode(['crew_list_status' => $event->crew_list_status]) }}"
              data-form-action="{{ route('events.update', ['id' => $event->id, 'action' => 'update-details']) }}"
              role="button">[{{ array_get([-1 => 'hidden', 0 => 'closed', 1 => 'open'], $event->crew_list_status) }}]</span>
    @else
        <span class="crew-status">[{{ array_get([-1 => 'hidden', 0 => 'closed', 1 => 'open'], $event->crew_list_status) }}]</span>
    @endif
</h2>
@if(count($event->crew) > 0)
    <div class="container-fluid crew-list">
        @foreach($event->crew_list as $role => $crew_list)
            <div class="form-group">
                {!! Form::label('crew', $role . ':', ['class' => 'col-md-5 control-label']) !!}
                <div class="col-md-7">
                    @foreach($crew_list as $crew)
                        @if($canEdit && is_object($crew))
                            <p class="form-control-static"
                               data-toggle="modal"
                               data-target="#modal"
                               data-modal-class="modal-sm"
                               data-modal-template="event_crew"
                               data-modal-title="Edit Crew Role"
                               data-form-action="{{ route('events.update', ['id' => $event->id, 'action' => 'update-crew']) }}"
                               data-form-data="{{ json_encode(['id' => $crew->id, 'name' => $crew->name ?: '', 'user' => $crew->user->name, 'em' => $crew->em, 'core' => !is_null($crew->name)]) }}"
                               data-editable="true"
                               role="button">{{ $crew->user->name }}</p>
                        @else
                            <p class="form-control-static">{{ $crew->user->name }}</p>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@else
    <p>No one is crewing this event yet</p>
@endif
<p>
    @if($event->crewListOpen() && !$isEM)
        @if($event->isCrew($user))
            <button class="btn btn-danger" data-submit-ajax="{{ route('events.volunteer', ['id' => $event->id]) }}" type="button">
                <span class="fa fa-user-times"></span>
                <span>Unvolunteer</span>
            </button>
        @else
            <button class="btn btn-success" data-submit-ajax="{{ route('events.volunteer', ['id' => $event->id]) }}" type="button">
                <span class="fa fa-user-plus"></span>
                <span>Volunteer</span>
            </button>
        @endif
    @endif
    @if($canEdit)
        <button class="btn btn-success"
                data-toggle="modal"
                data-target="#modal"
                data-modal-template="event_crew"
                data-modal-class="modal-sm"
                data-modal-title="Add Crew Role"
                data-form-action="{{ route('events.update', ['id' => $event->id, 'action' => 'add-crew']) }}"
                type="button">
            <span class="fa fa-user-plus"></span>
            <span>Add crew</span>
        </button>
        @if(count($event->crew) > 0)
        <button class="btn btn-success"
                data-toggle="modal"
                data-target="#modal"
                data-modal-template="event_emails"
                data-modal-title="Email Crew"
                data-modal-class="modal-sm"
                type="button">
            <span class="fa fa-envelope"></span>
            <span>Email Crew</span>
        </button>
        @endif
    @endif
</p>