@extends('app')
@section('page-section', 'polls')
@section('title', 'Polls')

@section('content')
    <h1 class="page-header">Polls</h1>
    @if(count($polls) > 0)
        {!! Form::open(['id' => 'listPolls']) !!}
        @foreach($polls as $i => $poll)
            <div class="poll">
                @if(Auth::user()->can('admin'))
                    <a class="btn btn-sm btn-danger btn-actions" href="{{ route('polls.delete', $poll->id) }}" onclick="return confirm('Are you sure you wish to delete this poll?\n\nThis process is irreversible.');" title="Delete this poll">
                        <span class="fa fa-trash"></span>
                    </a>
                @endif
                <h3>#{{ ($polls->currentPage() - 1) * $polls->perPage() + $i + 1 }} - <a href="{{ route('polls.view', $poll->id) }}">{{ $poll->question }}</a> [{{ $poll->totalVotes() }} vote{{ $poll->totalVotes() == 1 ? '' : 's' }}]</h3>
                @if($poll->description)
                    <div class="description">{{ $poll->description }}</div>
                @endif
                @if($poll->canViewResults(Auth::user()))
                    <ul class="options">
                        @foreach($poll->options as $option)
                            <li>
                                <span class="text">{{ $option->text }} &#8211; </span>
                                <span class="votes">{{ number_format($option->percentage(), 1) }}%</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
        {!! Form::close() !!}
        @include('partials.app.pagination', ['paginator' => $polls])
    @else
        <h3 style="margin-bottom:1.5em; text-align: center;">We don't yet have any polls.</h3>
    @endif
    @if(Auth::user()->can('admin'))
        <p>
            <a class="btn btn-success" href="{{ route('polls.create') }}">
                <span class="fa fa-plus"></span>
                <span>Create a new poll</span>
            </a>
        </p>
    @endif
@endsection