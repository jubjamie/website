@extends('app')

@section('title', 'Resources')

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
    <h1 class="page-header">@yield('title')</h1>
    <h2 class="page-header">Manage Tags</h2>
    <table class="table table-striped table-responsive">
        <thead>
            <tr>
                <th class="id">ID</th>
                <th>Name</th>
                <th>Slug</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if(count($tags) > 0)
                @foreach($tags as $tag)
                    <tr>
                        <td class="id">{{ $tag->id }}</td>
                        <td>{{ $tag->name }}</td>
                        <td class="slug">{{ $tag->slug }}</td>
                        <td class="admin-tools admin-tools-icon">
                            <div class="dropdown admin-tools">
                                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    <span class="fa fa-cog"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a href="#"
                                           data-toggle="modal"
                                           data-target="#modal"
                                           data-modal-class="modal-sm"
                                           data-modal-template="manage_tag"
                                           data-modal-title="Edit Tag"
                                           data-mode="edit"
                                           data-form-data="{{ json_encode(['id' => $tag->id, 'name' => $tag->name, 'slug' => $tag->slug]) }}"
                                           data-form-action="{{ route('resources.tag.update', ['id' => $tag->id]) }}">
                                            <span class="fa fa-pencil"></span> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#"
                                           data-submit-ajax="{{ route('resources.tag.delete', $tag->id) }}"
                                           data-submit-confirm="Really delete this tag?"
                                           onclick="return false;">
                                            <span class="fa fa-trash"></span> Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4">No tags.</td>
                </tr>
            @endif
        </tbody>
    </table>
    <div class="btn-group">
        <button class="btn btn-success"
                data-toggle="modal"
                data-target="#modal"
                data-modal-class="modal-sm"
                data-modal-template="manage_tag"
                data-modal-title="Create Tag"
                data-form-action="{{ route('resources.tag.create') }}"
                data-mode="create"
                type="button">
            <span class="fa fa-plus"></span>
            <span>Add a tag</span>
        </button>
        <a class="btn btn-danger" href="{{ route('resources.search') }}">
            <span class="fa fa-long-arrow-left"></span>
            <span>Back to search</span>
        </a>
    </div>
    @include('partials.app.pagination', ['paginator' => $tags])
@endsection

@section('modal')
    @include('resources.tags.modal')
@endsection