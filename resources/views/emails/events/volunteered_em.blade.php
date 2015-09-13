@extends('emails.base')

@section('title', "Hi {$em},")

@section('content')
   <p>This is just to let you know that {{ $user }} has volunteered to crew your event <strong>{{ $event }}</strong>.</p>
@endsection