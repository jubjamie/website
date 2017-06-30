@extends('app.main')

@section('page-section', 'resources')
@section('page-id', 'tag-index')
@section('title', 'Manage Tags :: Resources')
@section('header-main', 'Resources')
@section('header-sub', 'Manage Tags')

@section('scripts')
    $modal.on('show.bs.modal', function(event) {
    var target = $(event.relatedTarget);
    var mode = target.data('mode');
    var btn = $modal.find('button');
    var btn_span = btn.find('span').eq(1);

    btn.data('formAction', target.data('formAction'));
    if(mode == 'create') {
    btn_span.text('Create Tag');
    } else if(mode == 'edit') {
    btn_span.text('Save Changes');
    }
    });
@endsection

@section('content')
    <div>
        <button class="btn btn-success"
                data-toggle="modal"
                data-target="#modal"
                data-modal-class="modal-sm"
                data-modal-template="tag"
                data-modal-title="Create Tag"
                data-form-action="{{ route('resource.tag.store') }}"
                data-mode="create"
                type="button">
            <span class="fa fa-plus"></span>
            <span>Add a tag</span>
        </button>
        <span class="form-link">
            or {!! link_to_route('resource.index', 'Cancel') !!}
        </span>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="id">ID</th>
                <th col="name">Name</th>
                <th col="slug">Slug</th>
                <th class="admin-tools admin-tools-icon"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($tags as $tag)
                <tr>
                    <td class="id">{{ $tag->id }}</td>
                    <td col="name">{{ $tag->name }}</td>
                    <td col="slug">{{ $tag->slug }}</td>
                    <td class="admin-tools admin-tools-icon">
                        <div class="dropdown pull-right">
                            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <span class="fa fa-cog"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <button data-toggle="modal"
                                            data-target="#modal"
                                            data-modal-class="modal-sm"
                                            data-modal-template="tag"
                                            data-modal-title="Edit Tag"
                                            data-mode="edit"
                                            data-form-data="{{ json_encode($tag) }}"
                                            data-form-action="{{ route('resource.tag.update', ['id' => $tag->id]) }}">
                                        <span class="fa fa-pencil"></span> Edit
                                    </button>
                                </li>
                                <li>
                                    <button data-submit-ajax="{{ route('resource.tag.destroy', $tag->id) }}"
                                            data-submit-confirm="Are you sure you want to delete this tag?">
                                        <span class="fa fa-trash"></span> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No tags.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{ $tags }}
@endsection

@section('modal')
    @include('resources.modals.tag')
@endsection