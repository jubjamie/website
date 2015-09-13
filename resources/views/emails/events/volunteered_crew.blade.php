@extends('emails.base')

@section('title', "Hi {$user},")

@section('content')
    <p>Thanks for volunteering to crew the event <strong>{{ $event }}</strong>.</p>
    @if($em)
        <p>The event manager, {{ $em }}, will be in touch shortly.</p>
    @else
        <p>This event doesn't have an event manager yet, but when it does they will get in touch regarding the event's details.</p>
    @endif
@endsection