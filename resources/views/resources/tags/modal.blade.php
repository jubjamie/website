<div data-type="modal-template" data-id="manage_tag">
    {!! Form::open(['class' => 'form-horizontal']) !!}
    <div class="modal-body">
        <div class="form-group">
            {!! Form::label('name', 'Name:', ['class' => 'control-label col-xs-3']) !!}
            <div class="col-xs-9">
                {!! Form::text('name', null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('slug', 'Slug:', ['class' => 'control-label col-xs-3']) !!}
            <div class="col-xs-9">
                {!! Form::text('slug', null, ['class' => 'form-control', 'placeholder' => 'Leave blank to use default']) !!}
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-success" data-type="submit-modal" type="button">
            <span class="fa fa-check"></span>
            <span></span>
        </button>
    </div>
    {!! Form::close() !!}
</div>