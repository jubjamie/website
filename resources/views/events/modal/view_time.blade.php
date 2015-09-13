{!! Form::open(['route' => ['events.update', $event->id], 'class' => 'form-horizontal']) !!}
<div class="modal-body">
    {{-- Name --}}
    <div class="form-group">
        {!! Form::label('name', 'Title:', ['class' => 'col-xs-3 control-label']) !!}
        <div class="col-xs-9">
            {!! Form::text('name', null, ['class' => 'form-control']) !!}
        </div>
    </div>
    {{-- Date --}}
    <div class="form-group">
        {!! Form::label('date', 'Date:', ['class' => 'col-xs-3 control-label']) !!}
        <div class="col-xs-9">
            {!! Form::text('date', null, ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy']) !!}
        </div>
    </div>
    {{-- Time --}}
    <div class="form-group">
        {!! Form::label('start_time', 'Time:', ['class' => 'col-xs-3 control-label']) !!}
        <div class="col-xs-4">
            {!! Form::text('start_time', null, ['class' => 'form-control', 'placeholder' => 'hh:mm'])!!}
        </div>
        <div class="col-xs-1" style="padding: 0;">
            <p class="form-control-static text-center" style="margin:3px 0 0;">to</p>
        </div>
        <div class="col-xs-4">
            {!! Form::text('end_time', null, ['class' => 'form-control', 'placeholder' => 'hh:mm'])!!}
        </div>
    </div>
    {!! Form::input('hidden', 'id', null) !!}
</div>
<div class="modal-footer">
    <button class="btn btn-success" data-type="submit-modal" id="submitTimeModal" type="button">
        <span class="fa fa-check"></span>
        <span>Add Time</span>
    </button>
    <button class="btn btn-danger"
            data-type="submit-modal"
            data-submit-confirm="Are you sure you wish to delete this event time?"
            data-form-action="{{ route('events.update', ['id' => $event->id, 'action' => 'delete-time']) }}"
            id="deleteTime"
            type="button">
        <span class="fa fa-remove"></span>
        <span>Delete</span>
    </button>
</div>
{!! Form::close() !!}