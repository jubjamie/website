@extends('emails.base')
@include('emails.partials.blockquote')

@section('title', "Hi {$name},")

@section('content')
    <p>An administrator has just reset your password. Your new password is:</p>
    <blockquote @yield('_blockquote')>
        {{ $password }}
    </blockquote>
    <p>You can use this to {!! link_to_route('auth.login', 'log in') !!} and you can change it on {!! link_to_route('members.myprofile', 'your profile') !!}.</p>
@endsection