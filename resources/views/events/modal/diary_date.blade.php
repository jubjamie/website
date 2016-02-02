{!! Form::open() !!}
<div class="modal-body">
    <div class="row">
        <div class="col-xs-8">
            {!! Form::selectMonth('month', $date->month, ['class' => 'form-control']) !!}
        </div>
        <div class="col-xs-4">
            {!! Form::selectRange('year', $date->year - 5, $date->year + 5, $date->year, ['class' => 'form-control']) !!}
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
</div>
{!! Form::close() !!}