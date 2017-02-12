@extends('app.main')

@section('title', 'The Committee')
@section('page-section', 'committee')
@section('page-id', 'view')
@section('header-main', 'The Committee')

@section('scripts')
    $modal.on('show.bs.modal', function(event) {
        var btn = $(event.relatedTarget);
        var form = $modal.find('form');
        var submitBtn = $modal.find('#modalSubmit');

        form.find('select[name=order]').find('option').removeAttr('disabled');
        submitBtn.data('formAction', btn.data('formAction'));
        if(btn.data('mode') == 'edit') {
            form.find('select[name=order]').find('option[value=' + (btn.data('formData')['order'] + 1) + ']').attr('disabled', 'disabled');
            submitBtn.children('span:first').attr('class', 'fa fa-check');
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
    @forelse($roles as $role)
        @include('committee._position', ['role' => $role])
    @empty
        <h4 class="no-committee">We don't seem to have any committee roles ...</h4>
    @endforelse
    @can('create', \App\CommitteeRole::class)
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
    @endcan
@endsection

@section('modal')
    @can('create', \App\CommitteeRole::class)
        <div data-type="modal-template" data-id="committee_add">
            @include('committee.form')
        </div>
    @endcan
@endsection