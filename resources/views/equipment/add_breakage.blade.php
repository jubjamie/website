@extends('app')

@section('title', 'Report a Breakage')

@section('content')
    <h1 class="page-header">@yield('title')</h1>
    {!! Form::model(new App\EquipmentBreakage(), ['style' => 'max-width: 550px']) !!}
        <p>If you discover a piece of equipment is broken please fill in the form form below, so that the E&S officer is informed of the breakage. Please also label the equipment and take it to the Drama Store.</p>

        {{-- Equipment name --}}
        <div class="form-group @include('partials.form.error-class', ['name' => 'name'])">
            {!! Form::label('name', 'Equipment Name:', ['class' => 'control-label']) !!}
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'What\'s broken?']) !!}
            </div>

            @include('partials.form.input-error', ['name' => 'name'])
        </div>

        {{-- Equipment location --}}
        <div class="form-group @include('partials.form.error-class', ['name' => 'location'])">
            {!! Form::label('location', 'Equipment Location:', ['class' => 'control-label']) !!}
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-map-marker"></span></span>
                {!! Form::text('location', null, ['class' => 'form-control', 'placeholder' => 'Where is the equipment currently?']) !!}
            </div>
            @include('partials.form.input-error', ['name' => 'location'])
        </div>

        {{-- Marked --}}
        <div class="form-group @include('partials.form.error-class', ['name' => 'label'])">
            {!! Form::label('label', 'Labelling:', ['class' => 'control-label']) !!}
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-tag"></span></span>
                {!! Form::text('label', null, ['class' => 'form-control', 'placeholder' => 'How is the item marked as broken?']) !!}
            </div>
            @include('partials.form.input-error', ['name' => 'label'])
        </div>

        {{-- Damage description --}}
        <div class="form-group @include('partials.form.error-class', ['name' => 'description'])">
            {!! Form::label('description', 'Damage Description:', ['class' => 'control-label']) !!}
            <div class="input-group textarea">
                <span class="input-group-addon"><span class="fa fa-quote-left"></span></span>
                {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'What is wrong with it? Please be specific','rows' => 5]) !!}
            </div>
            @include('partials.form.input-error', ['name' => 'description'])
        </div>

        {{-- Buttons --}}
        <div class="form-group">
            <div class="btn-group">
                <button class="btn btn-success" disable-submit="Reporting breakage">
                    <span class="fa fa-check"></span>
                    <span>Add breakage</span>
                </button>
                <a class="btn btn-danger" href="{{ route('equipment.repairs') }}">
                    <span class="fa fa-long-arrow-left"></span>
                    <span>Cancel</span>
                </a>
            </div>
        </div>
    {!! Form::close() !!}
@endsection