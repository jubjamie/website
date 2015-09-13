{!! Form::open(['class' => 'form-horizontal']) !!}
<div class="modal-body">
    {{-- User --}}
    <div class="form-group">
        {!! Form::label('user_id', 'Member:', ['class' => 'col-xs-3 control-label']) !!}
        <div class="col-xs-9">
            <div>
            {!! Form::select('user_id', $users_crew, null, ['class' => 'form-control']) !!}
            </div>
            <p class="form-control-static" id="existingCrewUser"></p>
        </div>
    </div>
    {{-- Core --}}
    <div class="form-group">
        <div class="col-xs-12">
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('core', 1, null) !!}
                    Make this user core crew
                </label>
            </div>
        </div>

    </div>
    {{-- Core title --}}
    <div class="form-group">
        <div class="col-xs-12">
            {!! Form::text('name', null, ['class' => 'form-control', 'disabled' => 'disabled', 'placeholder' => 'Role name']) !!}
        </div>
    </div>
    {{-- EM --}}
    <div class="form-group">
        <div class="col-xs-12">
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('em', 1, null, ['disabled' => 'disabled']) !!}
                    This is an EM role
                </label>
            </div>
        </div>
    </div>
    {{-- Crew id --}}
    {!! Form::input('hidden', 'id', null) !!}
</div>
<div class="modal-footer">
    <button class="btn btn-success" data-type="submit-modal" id="submitCrewModal" type="button">
        <span class="fa fa-check"></span>
        <span>Add Crew</span>
    </button>
    <button class="btn btn-danger" data-type="submit-modal" data-submit-confirm="Are you sure you want to delete this crew role?" data-form-action="{{ route('events.update', ['id' => $event->id, 'action' => 'delete-crew']) }}" id="deleteCrew" type="button">
        <span class="fa fa-remove"></span>
        <span>Delete</span>
    </button>
</div>
{!! Form::close() !!}