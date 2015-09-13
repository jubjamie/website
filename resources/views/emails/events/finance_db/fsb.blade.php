@extends('emails.base')

@section('title', 'Hi ' . $name . ',')

@section('content')
    <p>Due to the nature of your event (<strong>{{ $event }}</strong>) I would like you to fill in the following online budget tracker as you plan your event, this will allow me to keep a better eye on the events finances and process yellow forms quickly.</p>
    <p><a href="http://bts-finance.co.uk/emfinance/{{ $event_id }}">EM Finance</a></p>
@endsection