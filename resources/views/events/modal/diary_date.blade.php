{!! Form::open() !!}
<div class="modal-body">
    <div class="row">
        <div class="col-xs-8">
            {!! Form::selectMonth('month', date('m'), ['class' => 'form-control']) !!}
        </div>
        <div class="col-xs-4">
            {!! Form::selectRange('year', date('Y') - 5, date('Y') + 5, date('Y'), ['class' => 'form-control']) !!}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button class="btn btn-success"
            data-url="{{ $redirectUrl }}"
            id="submitDateModal"
            type="button">
        <span class="fa fa-check"></span>
        <span>Change date</span>
    </button>
    <button class="btn btn-danger" data-toggle="modal" data-target="#modal" type="button">
        <span class="fa fa-undo"></span>
        <span>Cancel</span>
    </button>
</div>
{!! Form::close() !!}