@extends('app')

@section('title', 'Training Skills')

@section('stylesheets')
    @include('partials.tags.style', ['path' => 'partials/training'])
@endsection

@section('scripts')
    $modal.on('show.bs.modal', function(event) {
        if($(event.relatedTarget).data('modalTemplate') == 'new_category') {
            var btn = $modal.find('#submitCategory');
            var formAction = $(event.relatedTarget).data('formAction');
            btn.data('formAction', formAction);

            if(formAction.indexOf('update') > -1) {
                btn.find('span').eq(1).text('Update');
            }
        }
    });
@endsection

@section('content')
    <h1 class="page-header">@yield('title')</h1>
    <div class="container-fluid" id="listSkills">
        <div class="row">
            <div class="col-md-4">
                <nav>
                    <ul class="nav nav-pills nav-stacked category-list" role="tablist">
                        @foreach($skillCategories as $category)
                            <li>
                                <a data-toggle="tab" href="#{{ $category->id ? "category_{$category->id}" : "uncategorised" }}" role="button">{{ $category->name }}</a>
                                @if($activeUser->isAdmin() && $category->id)
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-warning btn-sm"
                                                data-toggle="modal"
                                                data-target="#modal"
                                                data-modal-class="modal-sm"
                                                data-modal-template="new_category"
                                                data-modal-title="Edit Category"
                                                data-form-data="{{ json_encode(['name' => $category->name]) }}"
                                                data-form-action="{{ route('training.category.update', $category->id) }}"
                                                type="button"
                                                title="Edit this category">
                                            <span class="fa fa-pencil"></span>
                                        </button>
                                        <button class="btn btn-danger btn-sm"
                                                data-submit-ajax="{{ route('training.category.delete', $category->id) }}"
                                                data-submit-confirm="Are you sure you want to delete this category? Any skills will become 'uncategorised'"
                                                type="button"
                                                title="Delete this category">
                                            <span class="fa fa-remove"></span>
                                        </button>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </nav>
                @if($activeUser->isAdmin())
                    <div>
                        <div class="btn-group">
                            <button class="btn btn-success"
                                    data-toggle="modal"
                                    data-target="#modal"
                                    data-modal-class="modal-sm"
                                    data-modal-template="new_category"
                                    data-modal-title="Add a Category"
                                    data-form-action="{{ route('training.category.add') }}"
                                    type="button">
                                <span class="fa fa-plus"></span>
                                <span>Add category</span>
                            </button>
                            <a class="btn btn-success" href="{{ route('training.skills.add') }}">
                                <span class="fa fa-plus"></span>
                                <span>Add skill</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-md-8">
                <div class="tab-content">
                    <div class="tab-pane active">
                        @include('training.skills._welcome')
                    </div>
                    @foreach($skillCategories as $category)
                        @include('training.skills._category')
                    @endforeach
                </div>
                <div class="btn-group">
                    @if($activeUser->isMember())
                        <a class="btn btn-success"
                           data-toggle="modal"
                           data-target="#modal"
                           data-modal-class="modal-sm"
                           data-modal-template="propose_skill"
                           data-modal-title="Propose Skill Level">
                            <span class="fa fa-plus"></span>
                            <span>Propose skill level</span>
                        </a>
                    @endif
                    <a class="btn btn-success"
                       data-toggle="modal"
                       data-target="#modal"
                       data-modal-class="modal-sm"
                       data-modal-template="award_skill"
                       data-modal-title="Award Skill">
                        <span class="fa fa-user-plus"></span>
                        <span>Award skill</span>
                    </a>
                    @if($activeUser->isAdmin())
                        <a class="btn btn-danger"
                           data-toggle="modal"
                           data-target="#modal"
                           data-modal-class="modal-sm"
                           data-modal-template="revoke_skill">
                            <span class="fa fa-user-times"></span>
                            <span>Revoke skill</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @if(Auth::check() && Auth::user()->isAdmin())
        <div data-type="modal-template" data-id="new_category">
            {!! Form::open(['class' => 'form-horizontal']) !!}
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('name', 'Name:', ['class' => 'col-xs-3 control-label']) !!}
                    <div class="col-xs-9">
                        {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" data-type="submit-modal" id="submitCategory" type="button">
                    <span class="fa fa-check"></span>
                    <span>Add category</span>
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    @endif
    @include('training.skills.modal.award')
    @include('training.skills.modal.propose')
    @include('training.skills.modal.revoke')
@endsection