@extends('app')
@section('page-section', 'resources')
@section('page-id', 'view')
@section('title', $resource->title . ' :: Resources')

@section('content')
    <h1 class="page-header">@yield('title')</h1>
    <div id="resourceWrapper">
        <h2>{{ $resource->title }}</h2>
        <h4 class="category">{{ $resource->category_name }}</h4>
        <iframe src="{{ route('resources.stream', ['id' => $resource->id]) }}"
                width="100%"
                height="600"
                frameborder="0"
                marginheight="0"
                marginwidth="0">Loading...
        </iframe>
        <div id="resourceDetailsWrapper">
            <div id="resourceLink">
                <div class="btn-group">
                    @if($resource->isFile())
                        <a class="btn btn-success" href="{{ route('resources.download', ['id' => $resource->id]) }}">
                            <span class="fa fa-cloud-download"></span>
                            <span>Download</span>
                        </a>
                    @elseif($resource->isGDoc())
                        <a class="btn btn-success" href="{{ $resource->getFilePath() }}" target="_blank">
                            <span class="fa fa-external-link"></span>
                            <span>Open</span>
                        </a>
                    @endif
                    @if($activeUser->isAdmin())
                        <a class="btn btn-primary" href="{{ route('resources.edit', ['id' => $resource->id]) }}">
                            <span class="fa fa-pencil"></span>
                            <span>Edit</span>
                        </a>
                    @endif
                </div>
            </div>
            <div id="resourceDetails">
                <p>Added by {{ $resource->author->name }} {{ $resource->created_at->diffForHumans() }} | Last
                    updated {{ $resource->updated_at->diffForHumans() }}</p>
                @if($resource->isAttachedToEvent())
                    <p>Related event: <a class="grey"
                                         href="{{ route('events.view', ['id' => $resource->event->id]) }}"
                                         target="_blank">{{ $resource->event->name }}</a></p>
                @endif
            </div>
        </div>
        @if($resource->description)
            <div id="resourceDescription">
                <p>{!! str_replace(PHP_EOL, '</p><p>', $resource->description) !!}</p>
            </div>
        @endif
        <ul class="tag-list">
            @foreach($resource->tags()->get() as $tag)
                <li>@include('resources.partials.tag')</li>
            @endforeach
        </ul>
        <a class="btn btn-primary" href="" id="back" onclick="history.back();">
            <span class="fa fa-long-arrow-left"></span>
            <span>Back</span>
        </a>
    </div>
@endsection