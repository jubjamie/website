@extends('emails.base')
@include('emails.partials.blockquote')

@section('title', 'Hi,')

@section('content')
    <p>{{ $user['forename'] }} {{ $user['surname'] }} has applied for <strong>Level {{ $proposal['proposed_level'] }}</strong> in the skill <strong>{{ $skill['name'] }}</strong>. The reasoning provided is:</p>
    <blockquote @yield('_blockquote')>
        {!! nl2br($proposal['reasoning']) !!}
    </blockquote>
    <p>You can view this proposal {!! link_to_route('training.skills.proposal.view', 'here', $proposal['id']) !!}.</p>
@endsection