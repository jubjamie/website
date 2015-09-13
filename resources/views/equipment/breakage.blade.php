@extends('app')

@section('title', 'Breakage Details')

@section('content')
    <h1 class="page-header">@yield('title')</h1>
    @if($breakage->closed)
        <div class="text-center" style="margin:-1.5em 0 1.5em;">
            <span class="label label-primary" style="font-size: 1.1em;">marked as closed</span>
        </div>
    @endif
    {!! Form::model($breakage, ['class' => 'form-horizontal', 'style' => 'max-width:600px;']) !!}
        {{-- Item name --}}
        <div class="form-group">
            {!! Form::label('name', 'Item:', ['class' => 'control-label col-md-4']) !!}
            <div class="col-md-8">
                <p class="form-control-static">{{ $breakage->name }}</p>
            </div>
        </div>

        {{-- Location --}}
        <div class="form-group">
            {!! Form::label('location', 'Location:', ['class' => 'control-label col-md-4']) !!}
            <div class="col-md-8">
                <p class="form-control-static">{{ $breakage->location }}</p>
            </div>
        </div>

        {{-- Label --}}
        <div class="form-group">
            {!! Form::label('label', 'Labelled as:', ['class' => 'control-label col-md-4']) !!}
            <div class="col-md-8">
                <p class="form-control-static">{{ $breakage->label }}</p>
            </div>
        </div>

        {{-- Description --}}
        <div class="form-group">
            {!! Form::label('description', 'Details:', ['class' => 'control-label col-md-4']) !!}
            <div class="col-md-8">
                <p class="form-control-static">{!! nl2br($breakage->description) !!}</p>
            </div>
        </div>

        {{-- Reported by --}}
    <div class="form-group">
        {!! Form::label('user_id', 'Reported by:', ['class' => 'control-label col-md-4']) !!}
        <div class="col-md-8">
            <p class="form-control-static">{!! $breakage->user ? ($breakage->user->name . ' (' . $breakage->user->username . ')') : '<em>- unknown -</em>' !!}<br>{{ $breakage->created_at->diffForHumans() }}</p>
        </div>
    </div>

        {{-- Comment --}}
        <div class="form-group @include('partials.form.error-class', ['name' => 'comment'])">
            {!! Form::label('comment', 'E&S Comment:', ['class' => 'control-label col-md-4']) !!}
            <div class="col-md-8">
                @if(Auth::user()->can('admin'))
                    {!! Form::textarea('comment', null, ['class' => 'form-control', 'rows' => 4]) !!}
                    @include('partials.form.input-error', ['name' => 'comment'])
                @else
                    <p class="form-control-static">{!! $breakage->comment ? nl2br($breakage->comment) : '<em>- none -</em>' !!}</p>
                @endif
            </div>
        </div>

        {{-- Status --}}
        <div class="form-group @include('partials.form.error-class', ['name' => 'status'])">
            {!! Form::label('status', 'Status:', ['class' => 'control-label col-md-4']) !!}
            <div class="col-md-8">
                @if(Auth::user()->can('admin'))
                    {!! Form::select('status', \App\EquipmentBreakage::$status, null, ['class' => 'form-control']) !!}
                    @include('partials.form.input-error', ['name' => 'status'])
                @else
                    <p class="form-control-static">{{ \App\EquipmentBreakage::$status[$breakage->status] }}</p>
                @endif
            </div>
        </div>

        {{-- Buttons --}}
        @if(Auth::user()->can('admin'))
            <div class="form-group">
                <div class="col-md-4"></div>
                <div class="col-md-8">
                    <button class="btn btn-success" disable-submit="Updating ..." name="action" value="update">
                        <span class="fa fa-check"></span>
                        <span>Update breakage</span>
                    </button>
                    @if(!$breakage->closed)
                        <button class="btn btn-danger" disable-submit="Updating ..." name="action" value="close">
                            <span class="fa fa-times"></span>
                            <span>Mark as closed</span>
                        </button>
                    @endif
                </div>
            </div>
        @endif

        <hr>
        <a class="btn btn-danger" href="{{ route('equipment.repairs') }}">
            <span class="fa fa-long-arrow-left"></span>
            <span>Back to the repairs db</span>
        </a>
    {!! Form::close() !!}
@endsection