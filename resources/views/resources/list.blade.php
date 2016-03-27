@extends('app')
@section('page-section', 'resources')
@section('page-id', 'list')
@section('title', 'Resources')

@section('content')
    <h1 class="page-header">@yield('title')</h1>
    <div class="clearfix">
        <div class="pull-left">
            <a class="btn btn-success" href="{{ route('resources.create') }}">
                <span class="fa fa-cloud-upload"></span>
                <span>Add resource</span>
            </a>
        </div>
        <div class="pull-right">
            <span>
                Category:
                <select data-type="filter-select" data-url-base="{{ route('resources.index') }}">
                    <option value="">- all categories -</option>
                    @foreach($all_categories as $category)
                        <option value="category:{{ $category->slug }}" {{ $filter == "category:{$category->slug}" ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                |
            </span>
            <span>
                Access:
                <select data-type="filter-select" data-url-base="{{ route('resources.index') }}">
                    <option value="">- all access -</option>
                    <option value="access:null" {{ $filter == "access:null" ? 'selected' : '' }}>Everyone</option>
                    @foreach(\App\Resource::getAccessList(false) as $id => $access)
                        <option value="access:{{ $id }}" {{ $filter == "access:{$id}" ? 'selected' : '' }}>{{ $access }}</option>
                    @endforeach
                </select>
            </span>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="id">ID</th>
                <th class="name">Name</th>
                <th class="tags">Tags</th>
                <th class="access">Access</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($resources as $resource)
                <tr>
                    <td class="id">{{ $resource->id }}</td>
                    <td class="name dual-layer">
                        <div class="upper">
                            <a class="grey" href="{{ route('resources.view', ['id' => $resource->id]) }}">{{ $resource->title }}</a>
                        </div>
                        <div class="lower">
                            {{ $resource->category_name }}
                        </div>
                    </td>
                    <td class="tags">
                        @if($resource->tags()->count())
                            <ul class="tag-list">
                                @foreach($resource->tags()->get() as $tag)
                                    <li>@include('resources.partials.tag')</li>
                                @endforeach
                            </ul>
                        @endif
                    </td>
                    <td class="access">{{ $resource->access_name }}</td>
                    <td class="admin-tools admin-tools-icon">
                        @if($activeUser->isAdmin())
                            <div class="dropdown admin-tools">
                                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    <span class="fa fa-cog"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a href="{{ route('resources.edit', ['id' => $resource->id]) }}">
                                            <span class="fa fa-pencil"></span> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a data-submit-ajax="{{ route('resources.delete', ['id' => $resource->id]) }}"
                                           data-submit-confirm="Are you sure you want to delete this resource?">
                                            <span class="fa fa-trash"></span> Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>

                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No resources.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @include('partials.app.pagination', ['paginator' => $resources])
@endsection