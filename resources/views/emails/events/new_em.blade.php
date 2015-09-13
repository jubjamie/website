@extends('emails.base')

@section('title', "Hi {$user},")

@section('content')
    <p>You have been set as the event manager for the event <strong>{{ $event }}</strong>.</p>
    <p>You can access and edit the event's information here: {!! link_to_route('events.view', $event, $event_id) !!}</p>
@endsection