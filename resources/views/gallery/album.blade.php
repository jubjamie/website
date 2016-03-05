@extends('app')
@section('page-section', 'gallery')
@section('title', 'Gallery: ' . $album['name'])

@section('content')
    <h1>{{ $album['name'] }}</h1>
    <h4 class="header">{{ $album['count'] }} photos</h4>
    <div id="viewAlbum">
        @foreach($photos as $photo)
            <div class="box">
                <a href="{{ $photo['link'] }}" target="_blank">
                    <div class="photo" style="background-image:url({{ $photo['images'][2]['source'] }});"></div>
                </a>
                @if(isset($photo['name']))
                <p class="comment">{{ $photo['name'] }}</p>
                @endif
            </div>
        @endforeach
    </div>
    <a class="btn btn-danger" href="{{ route('gallery.index') }}">
        <span class="fa fa-long-arrow-left"></span>
        <span>Back</span>
    </a>
@endsection