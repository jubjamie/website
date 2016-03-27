@extends('app')
@section('page-section', 'resources')
@section('page-id', 'search')
@section('title', 'Search Results :: Resources')

@section('content')
    <h1 class="page-header">Resources</h1>
    {!! Form::open(['route' => 'resources.search.do', 'class' => 'search']) !!}
        @include('resources.partials.search_inputs')
        {!! Form::hidden('category', $search->category) !!}
        @foreach($search->tags as $tag)
            {!! Form::hidden('tag[]', $tag) !!}
        @endforeach
    {!! Form::close() !!}
    @if($resources->total())
    <div id="resultsCount">Showing {{ ($resources->currentPage() - 1) * $resources->perPage() + 1 }} to {{ min(($resources->currentPage()) * $resources->perPage(), $resources->total()) }} of {{ $resources->total() }} results</div>
    @endif
    <div id="searchResults">
        @forelse($resources as $resource)
            @include('resources.partials.search_result')
        @empty
            <div id="noResults">
                <h2>We couldn't find anything that matched your search.</h2>
                <p>Try being less specific or browsing by category or tag from the <a href="{{ route('resources.search') }}">homepage</a>.</p>
            </div>
        @endforelse
    </div>
    @include('partials.app.pagination', ['paginator' => $resources])
@endsection