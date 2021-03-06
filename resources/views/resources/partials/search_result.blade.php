<div class="search-result">
    <div class="icon">
        @if($resource->isGDoc())
            <span class="fa fa-google" title="Google Doc"></span>
        @elseif($resource->getFileExtension() == 'pdf')
            <span class="fa fa-file-pdf-o" title="PDF"></span>
        @else
            <span class="fa fa-file-o" title="File"></span>
        @endif
    </div>
    <div class="details">
        <h1><a href="{{ route('resources.view', ['id' => $resource->id]) }}">{{ $resource->title }}</a></h1>
        <h2>{{ $resource->category_name }}</h2>
        @if($resource->description)
            <div class="description">
                <p>{!! str_replace(PHP_EOL, "</p><p>", $resource->description) !!}</p>
            </div>
        @endif
        @if($resource->tags)
            <ul class="tags">
                @foreach($resource->tags()->get() as $tag)
                    <li>@include('resources.partials.tag')</li>
                @endforeach
            </ul>
        @endif
    </div>
</div>