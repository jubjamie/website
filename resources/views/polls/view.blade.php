@extends('app')

@section('title', 'Poll')

@section('stylesheets')
    @include('partials.tags.style', ['path' => 'partials/polls'])
@endsection

@section('content')
    <h1 class="page-header">Poll</h1>
    {!! Form::open(['id' => 'viewPoll']) !!}
        <div class="width-wrapper">
            <div class="poll-details-wrapper">
                <div class="question-mark">
                    <span class="fa fa-question"></span>
                </div>
                <div class="poll-details">
                    <h1>{{ $poll->question }}</h1>
                    @if($poll->description)
                        <h2>{{ $poll->description }}</h2>
                    @endif
                </div>
            </div>
            <div class="answer-wrapper">
                @foreach($poll->options as $option)
                <div class="answer">
                    <div class="answer-number">
                        {{ $option->number }}
                    </div>
                    <div class="answer-details">
                        <div class="answer-text">
                            {{ nl2br($option->text) }}
                        </div>
                        @if($poll->canViewResults(Auth::user()))
                        <div class="progress">
                            <div class="progress-bar"
                                 role="progressbar"
                                 aria-valuenow="{{ $option->percentage() }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100"
                                 style="width: {{ $option->percentage() }}%;">
                                {{ number_format($option->percentage(), 1) }}%
                            </div>
                        </div>
                        @endif
                        @if(!$poll->voted(Auth::user()))
                            <button class="btn btn-success btn-sm" disable-submit="Casting vote ..." formaction="{{ route('polls.vote', $poll->id) }}" name="vote" value="{{ $option->id }}">
                                <span class="fa fa-thumbs-up"></span>
                                <span>Vote for this option</span>
                            </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <h3 class="total">Votes: {{ $poll->totalVotes() }}</h3>
            <hr>
            <a class="btn btn-danger" href="{{ route('polls.index') }}">
                <span class="fa fa-long-arrow-left"></span>
                <span>Back to the list</span>
            </a>
        </div>
    {!! Form::close() !!}
@endsection