@extends('app')

@section('title', 'Everything is Awesome')

@section('content')
    <p style="margin-top:-2em;">
        <a class="grey" href="{{ route('home') }}">
            <span class="fa fa-home"></span>
            <span>Back to the homepage</span>
        </a>
    </p>
    <iframe width="970"
            height="546"
            src="https://www.youtube.com/embed/E8VUONhV9NY?autoplay=1&rel=0&controls=0&showinfo=0&vq=hd720"
            frameborder="0"
            allowfullscreen
            style="max-width:100%;"></iframe>
    <p class="text-center em" style="font-size:12px;">'Everything is Awesome' is copyright Tegan and Sara featuring the Lonely Island.<br>Backstage is in no
        way claiming ownership of the music, lyrics, video or other related content.</p>
@endsection