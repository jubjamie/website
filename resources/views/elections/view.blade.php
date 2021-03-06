@extends('app')
@section('page-section', 'elections')
@section('page-id', 'view')
@section('title', $election->title)

@section('content')
    <h1 class="page-header">Elections</h1>
    <h2 class="page-header">@yield('title')</h2>

    {{-- Dates --}}
    <div class="container-fluid" id="electionDates">
        {{-- Nominations --}}
        <div class="row">
            <div class="col-sm-4 heading">Nominations:</div>
            <div class="col-sm-8">
                @include('elections._date', ['date' => $election->nominations_start])
                &mdash;
                @include('elections._date', ['date' => $election->nominations_end])
            </div>
        </div>

        {{-- Hustings --}}
        <div class="row">
            <div class="col-sm-4 heading">Hustings:</div>
            <div class="col-sm-8">
                @include('elections._date', ['date' => $election->hustings_time])
                in
                {{ $election->hustings_location }}
            </div>
        </div>

        {{-- Voting --}}
        <div class="row">
            <div class="col-sm-4 heading">Voting:</div>
            <div class="col-sm-8">
                <div>
                    @include('elections._date', ['date' => $election->voting_start])
                    &mdash;
                    @include('elections._date', ['date' => $election->voting_end])
                </div>
                <div>
                    <div class="btn-group btn-group-sm">
                        @if($election->isVotingOpen())
                            <a class="btn btn-success" href="http://www.bathstudent.com/elections/vote/{{ $election->bathstudent_id ?: '' }}" target="_blank">
                                <span class="fa fa-check"></span>
                                <span>Vote now</span>
                            </a>
                        @endif
                        <button class="btn btn-primary" data-toggle="modal" data-target="#modal" data-modal-template="position_details">
                            Position Details
                        </button>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#modal" data-modal-template="election_procedure">
                            Election Procedure
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Position details --}}
    <div id="positions">
        <h2>Positions</h2>

        <div class="container-fluid">
            @foreach($election->positions as $index => $position)
                <div class="row position">
                    <div class="col-sm-5 name">{{ $position }}:</div>
                    <div class="col-sm-7 nominations">
                        <div class="container-fluid">
                            @forelse($election->getNominations($index) as $nominee)
                                <div class="row">
                                    @if($nominee->elected)
                                        <div class="elected-status" title="Elected"><span class="fa fa-check success "></span></div>
                                    @endif
                                    <div class="name">
                                        {!! link_to_route('members.profile', $nominee->user->name, ['username' => $nominee->user->username], ['class' => 'grey', 'target' => '_blank']) !!}
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <a class="btn btn-primary" href="{{ route('elections.manifesto', ['id' => $election->id, 'nomineeId' => $nominee->id]) }}" target="_blank">
                                            <span class="fa fa-file-pdf-o"></span>
                                            <span>Manifesto</span>
                                        </a>
                                        @if($activeUser->isAdmin() && $election->isNominationsOpen())
                                            <a class="btn btn-danger"
                                               data-submit-ajax="{{ route('elections.nomination.delete', ['id' => $election->id, 'nomineeId' => $nominee->id]) }}"
                                               data-submit-confirm="Are you sure you want to delete this nomination?">
                                                <span class="fa fa-trash"></span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="row">
                                    <div class="name">
                                        <em>No nominations</em>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($activeUser->isAdmin())
            <p>
                @if($election->isNominationsOpen())
                    <button class="btn btn-success" data-toggle="modal" data-target="#modal" data-modal-class="modal-sm" data-modal-template="nominate">
                        <span class="fa fa-user-plus"></span>
                        <span>Add Nominee</span>
                    </button>
                @endif
                @if($election->hasVotingClosed())
                    <button class="btn btn-success" data-toggle="modal" data-target="#modal" data-modal-class="modal-md" data-modal-template="elect">
                        <span class="fa fa-group"></span>
                        <span>Set committee</span>
                    </button>
                @endif
            </p>
        @endif
    </div>
@endsection

@section('modal')
    @if($activeUser->isAdmin())
        @if($election->isNominationsOpen())
            @include('elections.modals.nominate')
        @endif
        @if($election->hasVotingClosed())
            @include('elections.modals.elect')
        @endif
    @endif
    @include('elections.modals.position_details')
    @include('elections.modals.election_procedure')
@endsection