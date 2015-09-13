@extends('app')

@section('title', 'Gallery')

@section('stylesheets')
    @include('partials.tags.style', ['path' => 'partials/gallery'])
@endsection

@section('content')
    <h1 class="page-header">@yield('title')</h1>
    <div id="viewGallery">
        @if(count($albums) > 0)
            @foreach($albums as $album)
                <div class="box" onclick="document.location='{{ route('gallery.album', $album['id']) }}'">
                    <div class="photo" style="background-image:url(https://graph.facebook.com/{{ $album['id'] }}/picture);"></div>
                    <p>{{ $album['name'] }}</p>
                    <p class="photo-count">({{ $album['count'] }} photos)</p>
                </div>
            @endforeach
        @else
            <h3 class="no-entries">We don't have any galleries at the moment</h3>
            <h4 class="no-entries">Check back soon</h4>
        @endif
    </div>
@endsection