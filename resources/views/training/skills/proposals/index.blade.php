@extends('app')

@section('title', 'Skill Proposals')

@section('stylesheets')
    @include('partials.tags.style', ['path' => 'partials/training'])
@endsection

@section('scripts')
    $('#proposalTab').tabify();
@endsection

@section('content')
    <h1 class="page-header">@yield('title')</h1>
    <div id="listProposals">
        <div class="tabpanel" id="proposalTab">
            <ul class="nav nav-tabs">
                <li class="active" id="reviewedTab"><a href="#">Requiring Review</a></li>
                <li id="notReviewedTab"><a href="#">Reviewed</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active">
                    @include('training.skills.proposals._proposal_list', ['proposals' => $unawarded, 'isListOfReviewed' => false])
                </div>
                <div class="tab-pane">
                    @include('training.skills.proposals._proposal_list', ['proposals' => $awarded, 'isListOfReviewed' => true])
                    @include('partials.app.pagination', ['paginator' => $awarded->fragment('notReviewed')])
                </div>
            </div>
        </div>
        <p>
            <a class="btn btn-danger" href="{{ route('training.skills.index') }}">
                <span class="fa fa-long-arrow-left"></span>
                <span>Back</span>
            </a>
        </p>
    </div>
@endsection