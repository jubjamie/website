@extends('app')

@section('title', 'Review Skill Proposal')

@section('stylesheets')
    @include('partials.tags.style', ['path' => 'partials/training'])
@endsection

@section('content')
    <h1 class="page-header">@yield('title')</h1>
    <div id="viewProposal">
        {!! Form::open(['class' => 'form-horizontal']) !!}

        {{-- Member --}}
        <div class="form-group">
            {!! Form::label('user', 'Member:', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                <p class="form-control-static">{{ $proposal->user->name }} ({{ $proposal->user->username }})</p>
            </div>
        </div>

        {{-- Skill --}}
        <div class="form-group">
            {!! Form::label('skill', 'Skill:', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                <p class="form-control-static">{{ $proposal->skill->name }}<br>({{ $proposal->skill->category ? $proposal->skill->category->name : 'uncategorised' }})</p>
            </div>
        </div>

        {{-- Level --}}
        <div class="form-group">
            {!! Form::label('proposed_level', 'Level Requested:', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                <p class="form-control-static">{!! \App\TrainingSkill::getProficiencyHtml($proposal->proposed_level) !!}</p>
            </div>
        </div>

        {{-- Reason --}}
        <div class="form-group">
            {!! Form::label('reasoning', 'Reasoning:', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                <p class="form-control-static">{!! nl2br($proposal->reasoning) !!}</p>
            </div>
        </div>

        {{-- Awarded Level --}}
        <div class="form-group @include('partials.form.error-class', ['name' => 'awarded_level'])">
            {!! Form::label('awarded_level', 'Awarded Level:', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                @if($proposal->isAwarded())
                <p class="form-control-static">{!! $proposal->awarded_level == 0 ? '<em>&ndash; no level awarded &ndash;</em>' : \App\TrainingSkill::getProficiencyHtml($proposal->awarded_level) !!}</p>
                @else
                {!! Form::select('awarded_level', [0 => 'Do not award', 1 => 'Level 1', 2 => 'Level 2', 3 => 'Level 3'], $proposal->proposed_level, ['class' => 'form-control']) !!}
                @include('partials.form.input-error', ['name' => 'awarded_level'])
                @endif
            </div>
        </div>

        {{-- Comment --}}
        <div class="form-group @include('partials.form.error-class', ['name' => 'awarded_comment'])">
            {!! Form::label('awarded_comment', 'Comment:', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                @if($proposal->isAwarded())
                <p class="form-control-static">{!! $proposal->awarded_comment ? nl2br($proposal->awarded_comment) : '<em>&ndash; no comment provided &ndash;</em>' !!}</p>
                @else
                {!! Form::textarea('awarded_comment', null, ['class' => 'form-control', 'placeholder' => 'This is optional if you award a level, but nice to provide', 'rows' => 4]) !!}
                @include('partials.form.input-error', ['name' => 'awarded_comment'])
                @endif
            </div>
        </div>

        {{-- Awarded details --}}
        @if($proposal->isAwarded())
            <div class="form-group">
                {!! Form::label('awarded', 'Awarded by:', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    <p class="form-control-static">{{ $proposal->awarder->name }}<br>{{ $proposal->awarded_date->diffForHumans() }}</p>
                </div>
            </div>
        @endif

        {{-- Buttons --}}
        <div class="form-group">
            <div class="col-sm-4"></div>
            <div class="col-sm-8">
                @if($proposal->isAwarded())
                    <a class="btn btn-success" href="{{ route('training.skills.proposal.index') }}">
                        <span class="fa fa-long-arrow-left"></span>
                        <span>Back</span>
                    </a>
                @else
                    <button class="btn btn-success" disable-submit="Processing ...">
                        <span class="fa fa-check"></span>
                        <span>Submit</span>
                    </button>
                    <a class="btn btn-danger" href="{{ route('training.skills.proposal.index') }}">
                        <span class="fa fa-undo"></span>
                        <span>Cancel</span>
                    </a>
                    <a class="btn btn-primary"
                       data-toggle="modal"
                       data-target="#modal"
                       data-modal-template="skill_details"
                       data-modal-title="Skill Details"
                       href="#">
                        <span class="fa fa-question"></span>
                        <span>Skill details</span>
                    </a>
                @endif
            </div>
        </div>

        {!! Form::close() !!}
    </div>
@endsection

@section('modal')
    @if(!$proposal->isAwarded())
    <div data-type="modal-template" data-id="skill_details">
        <div class="modal-body">
            {!! Form::open(['class' => 'form-horizontal']) !!}
            {{-- Name --}}
            <div class="form-group">
                {!! Form::label('skill_name', 'Name:', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    <p class="form-control-static">{{ $proposal->skill->name }}</p>
                </div>
            </div>

            {{-- Category --}}
            <div class="form-group">
                {!! Form::label('skill_category', 'Category:', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    <p class="form-control-static">{!! $proposal->skill->category_id ? $proposal->skill->category->name : '<em>uncategorised</em>' !!}</p>
                </div>
            </div>

            {{-- Description --}}
            <div class="form-group">
                {!! Form::label('skill_description', 'Description:', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    <p class="form-control-static">{!! nl2br($proposal->skill->description) !!}</p>
                </div>
            </div>

            {{-- Level requirements --}}
            <h2 style="font-size: 18px;">Level Requirements</h2>
            <table class="table level-requirements">
                @for($i = 1; $i <= 3; $i++)
                    <tr>
                        <td>{!! \App\TrainingSkill::getProficiencyHtml($i) !!}</td>
                        <td>{!! nl2br($proposal->skill->{"requirements_level{$i}"}) !!}</td>
                    </tr>
                @endfor
            </table>
            {!! Form::close() !!}
        </div>
        <div class="modal-footer">
            <button class="btn btn-success" data-toggle="modal" data-target="#modal" type="button">
                <span class="fa fa-check"></span>
                <span>Ok, got it</span>
            </button>
        </div>
    </div>
    @endif
@endsection