@extends('app')
@section('page-section', 'resources')
@section('page-id', 'search-index')
@section('title', 'Resources')

@section('content')
    <h1 class="page-header">@yield('title')</h1>
    {!! Form::open(['route' => 'resources.search.do', 'class' => 'search']) !!}
        @include('resources.partials.search_inputs')
        <div class="category-tag-summary">
            <fieldset>
                <legend>Categories</legend>
                @if(count($all_categories) > 0)
                    <div class="summary-wrapper">
                        <ul>
                        @foreach($all_categories as $category)
                            <li>{!! link_to_route('resources.search', $category->name, ['category' => $category->slug], ['class' => 'grey']) !!} ({{ $category->resources()->accessible()->count() }})</li>
                        @endforeach
                        </ul>
                    </div>
                @else
                    No categories.
                @endif
            </fieldset>
            <fieldset>
                <legend>Tags</legend>
                @if(count($all_tags) > 0)
                    <div class="summary-wrapper">
                        <ul class="tag-list">
                            @foreach($all_tags as $tag)
                                <li>@include('resources.partials.tag')</li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    No tags.
                @endif
            </fieldset>
        </div>
    {!! Form::close() !!}
@endsection