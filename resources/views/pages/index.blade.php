@extends('app')

@section('title', 'Webpage Manager')

@section('styles')
    table.table .page-title .title {
        font-size: 18px;
    }
    table.table .page-title .slug {
        color: #666;
        font-size: 14px;
    }
    table.table .page-author {
        width: 20%;
    }
    table.table .page-published {
        font-size: 20px;
        width: 2em;
    }
    table.table .page-published .fa-check {
        color: green;
    }
    table.table .page-actions {
        width: 6em;
    }
@endsection

@section('content')
    <h1 class="page-header">Webpage Manager</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="id hidden-xs">ID</th>
                <th>Page</th>
                <th class="text-center hidden-xs">Author</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if(count($pages))
                @foreach($pages as $page)
                <tr>
                    <td class="id hidden-xs">{{ $page->id }}</td>
                    <td class="page-title">
                        <div class="title">{!! link_to_route('page.edit', $page->title, $page->slug) !!}</div>
                        <div class="slug">{{ route('page.show', $page->slug, false) }}</div>
                    </td>
                    <td class="text-center page-author hidden-xs">{{ $page->author->name }}</td>
                    <td class="text-center page-published">
                        @if($page->published)
                            <span class="fa fa-check success" title="Published"></span>
                        @else
                            <span class="fa fa-remove danger" title="Not published"></span>
                        @endif
                    </td>
                    <td class="page-actions text-center">
                        <div class="btn-group">
                            <a class="btn btn-sm btn-bts" href="{{ route('page.edit', $page->slug) }}">
                                <span class="fa fa-pencil"></span>
                            </a>
                            <a class="btn btn-sm btn-danger" href="{{ route('page.destroy', $page->slug) }}" onclick="return confirm('Are you sure you wish to delete this page?\n\nThis process is irreversible.');">
                                <span class="fa fa-trash-o"></span>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5">No webpages</td>
                </tr>
            @endif
        </tbody>
    </table>
    <p class="text-right">
        <a class="btn btn-success" href="{{ route('page.create') }}">
            <span class="fa fa-plus"></span>
            <span>New Page</span>
        </a>
    </p>

    @include('partials.app.pagination', ['paginator' => $pages])
@endsection