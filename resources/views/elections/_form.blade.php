@extends('app')
@section('page-section', 'elections')

@section('scripts')
    $('select[name="type"]').on('change', function() {
        if($(this).val() == 1) {
            $('#position-list').addClass('hidden');
        } else {
            $('#position-list').removeClass('hidden');
        }
    });
@endsection

@section('content')
    <h1 class="page-header">Elections</h1>
    <h2 class="page-header">@yield('title')</h2>

    {!! Form::model($election, ['url' => $route, 'class' => 'form-horizontal']) !!}
        {{-- Election type --}}
        <div class="form-group @if ($errors->default->has('type')) has-error @endif">
            {!! Form::label('type', 'Election Type:', ['class' => 'control-label col-md-4']) !!}
            <div class="col-md-8">
                {!! Form::select('type', \App\Election::$Types, null, ['class' => 'form-control']) !!}
                @include('partials.form.input-error', ['name' => 'type'])
            </div>
        </div>

        {{-- BathStudent link --}}
        <div class="form-group @if ($errors->default->has('bathstudent_id')) has-error @endif">
            {!! Form::label('bathstudent_id', 'BathStudent Link:', ['class' => 'control-label col-md-4']) !!}
            <div class="col-md-8">
                {!! Form::text('bathstudent_id', null, ['class' => 'form-control']) !!}
                @include('partials.form.input-error', ['name' => 'bathstudent_id'])
            </div>
        </div>

        {{-- Hustings info --}}
        <div class="form-group @if ($errors->default->has('hustings_date')) has-error @endif">
            {!! Form::label('hustings_time', 'Hustings:', ['class' => 'control-label col-md-4']) !!}
            <div class="col-md-8">
                <div class="input-group">
                    {!! Form::datetime('hustings_time', null, ['class' => 'form-control']) !!}
                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                </div>
                @include('partials.form.input-error', ['name' => 'hustings_date'])
            </div>
        </div>
        <div class="form-group @if ($errors->default->has('hustings_location')) has-error @endif">
            {!! Form::label('hustings_location', 'Hustings Location:', ['class' => 'control-label col-md-4']) !!}
            <div class="col-md-8">
                {!! Form::text('hustings_location', null, ['class' => 'form-control']) !!}
                @include('partials.form.input-error', ['name' => 'hustings_location'])
            </div>
        </div>

        {{-- Nominations --}}
        <div class="form-group @if ($errors->default->has('nominations_start')) has-error @endif">
            {!! Form::label('nominations_start', 'Nominations Open:', ['class' => 'control-label col-md-4']) !!}
            <div class="col-md-8">
                <div class="input-group">
                    {!! Form::datetime('nominations_start', null, ['class' => 'form-control']) !!}
                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                </div>
                @include('partials.form.input-error', ['name' => 'nominations_start'])
            </div>
        </div>
        <div class="form-group @if ($errors->default->has('nominations_end')) has-error @endif">
            {!! Form::label('nominations_end', 'Nominations Close:', ['class' => 'control-label col-md-4']) !!}
            <div class="col-md-8">
                <div class="input-group">
                    {!! Form::datetime('nominations_end', null, ['class' => 'form-control']) !!}
                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                </div>
                @include('partials.form.input-error', ['name' => 'nominations_end'])
            </div>
        </div>

        {{-- Voting --}}
        <div class="form-group @if ($errors->default->has('voting_start')) has-error @endif">
            {!! Form::label('voting_start', 'Voting Opens:', ['class' => 'control-label col-md-4']) !!}
            <div class="col-md-8">
                <div class="input-group">
                    {!! Form::datetime('voting_start', null, ['class' => 'form-control']) !!}
                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                </div>
                @include('partials.form.input-error', ['name' => 'voting_start'])
            </div>
        </div>
        <div class="form-group @if ($errors->default->has('voting_end')) has-error @endif">
            {!! Form::label('voting_end', 'Voting Closes:', ['class' => 'control-label col-md-4']) !!}
            <div class="col-md-8">
                <div class="input-group">
                    {!! Form::datetime('voting_end', null, ['class' => 'form-control']) !!}
                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                </div>
                @include('partials.form.input-error', ['name' => 'voting_end'])
            </div>
        </div>

        {{-- Positions --}}
        <div class="form-group @if($election->isFull()) hidden @endif " id="position-list">
            {!! Form::label('', 'Positions:', ['class' => 'control-label col-md-4']) !!}
            <div class="col-md-8">
                <div class="container-fluid">
                    @foreach($positions as $i => $position)
                        <div class="form-group @if ($errors->default->has('positions['.$i.']')) has-error @endif">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('positions_checked[]', $i, true) !!}
                                    {{ $position }}
                                    {!! Form::hidden('positions['.$i.']', $position, ['class' => 'form-control form-control-inline', 'style' => '']) !!}
                                    @include('partials.form.input-error', ['name' => 'positions['.$i.']'])
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Buttons --}}
        <div class="form-group">
            <div class="col-md-4"></div>
            <div class="col-md-8">
                <div class="btn-group">
                    @if($route == route('elections.create.do'))
                        <button class="btn btn-success" disable-submit="Saving ...">
                            <span class="fa fa-plus"></span>
                            <span>Create Election</span>
                        </button>
                        <a class="btn btn-danger" href="{{ route('elections.index') }}">
                            <span class="fa fa-long-arrow-left"></span>
                            <span>Back</span>
                        </a>
                    @elseif($route == route('elections.update', $election->id))
                        <button class="btn btn-success" disable-submit="Saving ...">
                            <span class="fa fa-check"></span>
                            <span>Save</span>
                        </button>
                        <a class="btn btn-danger" href="{{ route('elections.view', ['id' => $election->id]) }}" onclick="history.back;">
                            <span class="fa fa-long-arrow-left"></span>
                            <span>Back</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    {!! Form::close() !!}
@endsection