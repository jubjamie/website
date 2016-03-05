{!! Form::open(['route' => ['events.update', $event->id], 'class' => 'form-horizontal']) !!}
<div class="modal-body">
    {{-- Name --}}
    <div class="form-group">
        {!! Form::label('name', 'Title:', ['class' => 'col-xs-3 control-label']) !!}
        <div class="col-xs-9">
            {!! Form::text('name', null, ['class' => 'form-control']) !!}
        </div>
    </div>
    {{-- Start --}}
    <div class="form-group">
        {!! Form::label('start', 'Start:', ['class' => 'control-label col-xs-3']) !!}
        <div class="col-xs-9">
            <div class="input-group">
                {!! Form::datetime('start',  null, ['class' => 'form-control', 'data-date-format' => 'YYYY-MM-DD HH:mm']) !!}
                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
            </div>
        </div>
    </div>
    {{-- End --}}
    <div class="form-group">
        {!! Form::label('end', 'Finish:', ['class' => 'control-label col-xs-3']) !!}
        <div class="col-xs-9">
            <div class="input-group">
                {!! Form::datetime('end', null, ['class' => 'form-control', 'data-date-format' => 'YYYY-MM-DD HH:mm']) !!}
                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
            </div>
        </div>
    </div>
    {!! Form::input('hidden', 'id', null) !!}
</div>
<div class="modal-footer">
    <div class="btn-group">
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
</div>
{!! Form::close() !!}