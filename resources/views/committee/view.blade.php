@extends('app')
@section('page-section', 'committee')
@section('title', 'The Committee')

@section('scripts')
    $modal.on('show.bs.modal', function(event) {
        var btn = $(event.relatedTarget);
        var form = $modal.find('form');
        var submitBtn = $modal.find('#modalSubmit');

        form.find('select[name=order]').find('option').removeAttr('disabled');
        submitBtn.data('formAction', btn.data('formAction'));
        if(btn.data('mode') == 'edit') {
            form.find('select[name=order]').find('option[value=' + (btn.data('formData')['order'] + 1) + ']').attr('disabled', 'disabled');
            submitBtn.children('span:first').attr('class', 'fa fa-refresh');
            submitBtn.children('span:last').text('Save changes');
            $modal.find('#modalDelete').show();
        } else {
            submitBtn.children('span:first').attr('class', 'fa fa-plus');
            submitBtn.children('span:last').text('Add role');
            $modal.find('#modalDelete').hide();
        }
    });
@endsection

@section('content')
    <h1 class="page-header">The Committee</h1>
    <div id="viewCommittee">
        @if(count($roles))
            <div class="container-fluid">
                @for($i = 0; $i < ceil(count($roles) / 2); $i++)
                    <div class="row">
                        @include('committee._position', ['role' => $roles[2 * $i]])
                        @include('committee._position', ['role' => isset($roles[2 * $i + 1]) ? $roles[2 * $i + 1] : null])
                    </div>
                @endfor
            </div>
        @else
            <h4 class="no-committee">We don't seem to have any committee roles ...</h4>
        @endif
        @if(Auth::check() && Auth::user()->can('admin'))
            <hr>
            <a class="btn btn-success"
               data-toggle="modal"
               data-target="#modal"
               data-modal-template="committee_add"
               data-modal-title="Add Committee Position"
               data-modal-class="modal-sm"
               data-form-action="{{ route('committee.add') }}"
               data-mode="add"
               href="#">
                <span class="fa fa-plus"></span>
                <span>Add a new role</span>
            </a>
        @endif
    </div>
@endsection

@section('modal')
    @if(Auth::check() && Auth::user()->can('admin'))
        <div data-type="modal-template" data-id="committee_add">
            @include('committee.form')
        </div>
    @endif
@endsection