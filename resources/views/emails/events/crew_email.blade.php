@extends('emails.base')

@section('title', 'Hi all,')

@section('content')
    {!! nl2br($body) !!}
@endsection

@if($name)
    @section('from', $name)
@endif