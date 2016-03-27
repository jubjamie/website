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
            btn_span.text('Create Category');
        } else if(mode == 'edit') {
            btn_span.text('Save Changes');
        }
    });
@endsection

@section('content')
    <h1 class="page-header">@yield('title')</h1>
    <h2 class="page-header">Manage Categories</h2>
    <table class="table table-striped table-responsive">
        <thead>
            <tr>
                <th class="id">ID</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Type</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if(count($categories) > 0)
                @foreach($categories as $category)
                    <tr>
                        <td class="id">{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td class="slug">{{ $category->slug }}</td>
                        <td>{{ \App\ResourceCategory::$Flags[(int)$category->flag] }}</td>
                        <td class="admin-tools admin-tools-icon">
                            <div class="dropdown admin-tools">
                                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    <span class="fa fa-cog"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a href="#" data-toggle="modal" data-target="#modal" data-modal-class="modal-sm" data-modal-template="manage_category" data-modal-title="Edit Category" data-mode="edit" data-form-data="{{ json_encode(['id' => $category->id, 'name' => $category->name, 'slug' => $category->slug, 'flag' => (int)$category->flag]) }}" data-form-action="{{ route('resources.category.update', ['id' => $category->id]) }}">
                                            <span class="fa fa-pencil"></span> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" data-submit-ajax="{{ route('resources.category.delete', $category->id) }}" data-submit-confirm="Really delete this category?" onclick="return false;">
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
                    <td colspan="5">No categories.</td>
                </tr>
            @endif
        </tbody>
    </table>
    <div class="btn-group">
        <button class="btn btn-success"
                data-toggle="modal"
                data-target="#modal"
                data-modal-class="modal-sm"
                data-modal-template="manage_category"
                data-modal-title="Create Category"
                data-form-action="{{ route('resources.category.create') }}"
                data-mode="create"
                type="button">
            <span class="fa fa-plus"></span>
            <span>Add a category</span>
        </button>
        <a class="btn btn-danger" href="{{ route('resources.search') }}">
            <span class="fa fa-long-arrow-left"></span>
            <span>Back to search</span>
        </a>
    </div>
    @include('partials.app.pagination', ['paginator' => $categories])
@endsection

@section('modal')
    @include('resources.categories.modal')
@endsection