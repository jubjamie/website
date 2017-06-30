<div data-type="modal-template" data-id="tag">
    {!! Form::open() !!}
    <div class="modal-body">
        <div class="form-group">
            {!! Form::label('name', 'Name:', ['class' => 'control-label']) !!}
            {!! Form::text('name', null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('slug', 'Slug:', ['class' => 'control-label']) !!}
            {!! Form::text('slug', null, ['class' => 'form-control', 'placeholder' => 'Leave blank to use default']) !!}
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-success" data-type="submit-modal" type="button">
            <span class="fa fa-check"></span>
            <span>Save Changes</span>
        </button>
    </div>
    {!! Form::close() !!}
</div>