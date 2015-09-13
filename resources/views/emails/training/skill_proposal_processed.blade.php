@extends('emails.base')
@include('emails.partials.blockquote')

@section('title', "Hi {$user},")

@section('content')
    <p>Your skill proposal for <strong>Level {{ $proposal['proposed_level'] }}</strong> in <strong>{{ $skill['name'] }}</strong> has been processed by {{ $awarder['forename'] }} {{ $awarder['surname'] }}.</p>
    @if($proposal['awarded_level'] == 0)
        <p>Unfortunately you haven't been awarded the skill this time. {{ $awarder['forename'] }}'s comments are included below.</p>
    @else
        <p>You have been awarded <strong>Level {{ $proposal['awarded_level'] }}</strong>. {{ $proposal['awarded_comment'] ? " {$awarder['forename']} gave you some comments, which are included below." : '' }}</p>
    @endif
    @if($proposal['awarded_comment'])
    <blockquote @yield('_blockquote')>
        {!! nl2br($proposal['awarded_comment']) !!}
    </blockquote>
    @endif
@endsection