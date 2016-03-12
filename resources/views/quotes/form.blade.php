<div class="modal-body">
    {!! Form::open() !!}
    {{-- Text field for the date --}}
    <div class="form-group">
        <div class="input-group">
            {!! Form::datetime('date', new \Carbon\Carbon(), ['class' => 'form-control']) !!}
            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
        </div>
    </div>

    {{-- Text field 'culprit' --}}
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon"><span class="fa fa-user"></span></span>
            {!! Form::text('culprit', null, ['class' => 'form-control', 'placeholder' => 'Who said it?']) !!}
        </div>
    </div>

    {{-- Textarea for the quote --}}
    <div class="form-group">
        <div class="input-group textarea">
            <span class="input-group-addon"><span class="fa fa-quote-left"></span></span>
            {!! Form::textarea('quote', null, ['class' => 'form-control', 'placeholder' => 'What was said?', 'rows' => 5]) !!}
        </div>
    </div>
    {!! Form::close() !!}
</div>
<div class="modal-footer">
    <button class="btn btn-success" data-type="submit-modal" id="addQuoteModal">
        <span class="fa fa-check"></span>
        <span>Add quote</span>
    </button>
</div>