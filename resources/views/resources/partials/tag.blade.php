<a class="label label-default" href="{{ route('resources.search', ['tag[]' => $tag->slug]) }}">
    <span class="fa fa-tag"></span>
    <span>{{ $tag->name }}</span>
</a>