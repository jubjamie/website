@extends('emails.base')

@section('title', 'Hi ' . $name . ',')

@section('content')
    <p>Please fill in the finance summary found at the link below for <strong>{{ $event }}</strong>, this will allow me to invoice the event quickly and efficiently.</p>
    <p><a href="http://bts-finance.co.uk/emfinance/{{ $event_id }}">EM Finance</a></p>
@endsection