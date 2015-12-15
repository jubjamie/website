@extends('app')

@section('title', 'Report an Accident')

@section('styles')
    @media (min-width: 992px) { fieldset.col-md-6:nth-child(even) { margin-left: 9px; width: 49%; }}
@endsection

@section('content')
    <h1 class="page-header">Report an Accident</h1>
    <p>Use this form to report an accident that occurred during a Backstage-supported event or activity. Please note that this form is automatically sent to the Students' Union as well as Backstage.</p>

    {!! Form::open(['class' => 'form-horizontal']) !!}
        <div class="row">
            <fieldset class="col-md-6">
                <legend>Accident Details</legend>

                <!-- Location -->
                <div class="form-group @include('partials.form.error-class', ['name' => 'location'])">
                    {!! Form::label('location', 'Location:', ['class' => 'control-label col-sm-3']) !!}
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="fa fa-home"></span></span>
                            {!! Form::text('location', null, ['class' => 'form-control', 'placeholder' => 'Where the accident occurred']) !!}
                        </div>
                        @include('partials.form.input-error', ['name' => 'location'])
                    </div>
                </div>

                <!-- Date -->
                <div class="form-group @include('partials.form.error-class', ['name' => 'date'])">
                    {!! Form::label('date', 'Date:', ['class' => 'control-label col-sm-3']) !!}
                    <div class="col-sm-9">
                        {!! Form::selectDate('date') !!}
                        @include('partials.form.input-error', ['name' => 'date'])
                    </div>
                </div>

                <!-- Time -->
                <div class="form-group @include('partials.form.error-class', ['name' => 'time'])">
                    {!! Form::label('time', 'Time:', ['class' => 'control-label col-sm-3']) !!}
                    <div class="col-sm-9">
                        {!! Form::selectTime('time') !!}
                        @include('partials.form.input-error', ['name' => 'time'])
                    </div>
                </div>

                <!-- Details -->
                <div class="form-group @include('partials.form.error-class', ['name' => 'details'])">
                    {!! Form::label('details', 'Details:', ['class' => 'control-label col-sm-3']) !!}
                    <div class="col-sm-9">
                        {!! Form::textarea('details', null, ['class' => 'form-control', 'rows' => 6, 'placeholder' => 'Brief description of the accident, include the location and any injuries caused']) !!}
                        @include('partials.form.input-error', ['name' => 'details'])
                    </div>
                </div>

                <!-- Severity -->
                <div class="form-group @include('partials.form.error-class', ['name' => 'severity'])">
                    {!! Form::label('severity', 'Severity:', ['class' => 'control-label col-sm-3']) !!}
                    <div class="col-sm-9">
                        {!! Form::select('severity', \App\Http\Requests\ContactAccidentRequest::$Severities, null, ['class' => 'form-control']) !!}
                        @include('partials.form.input-error', ['name' => 'severity'])
                    </div>
                </div>

                <!-- Absence Details -->
                <div class="form-group @include('partials.form.error-class', ['name' => 'absence_details'])">
                    {!! Form::label('absence_details', 'Absence:', ['class' => 'control-label col-sm-3']) !!}
                    <div class="col-sm-9">
                        {!! Form::textarea('absence_details', null, ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Details of any absence as a result of the accident']) !!}
                        @include('partials.form.input-error', ['name' => 'absence_details'])
                    </div>
                </div>
            </fieldset>

            <div class="col-md-6">
                <fieldset>
                    <legend>Injured Party Details</legend>

                    <!-- Name -->
                    <div class="form-group @include('partials.form.error-class', ['name' => 'injured_name'])">
                        {!! Form::label('injured_name', 'Injured Party:', ['class' => 'control-label col-sm-4']) !!}
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-user"></span></span>
                                {!! Form::text('injured_name', null, ['class' => 'form-control', 'placeholder' => 'The name of the injured person']) !!}
                            </div>
                            @include('partials.form.input-error', ['name' => 'injured_name'])
                        </div>
                    </div>

                    <!-- Person Type -->
                    <div class="form-group @include('partials.form.error-class', ['name' => 'person_type']) @include('partials.form.error-class', ['name' => 'person_type_other'])">
                        {!! Form::label('person_type', 'Category:', ['class' => 'control-label col-sm-4']) !!}
                        <div class="col-sm-8">
                            {!! Form::select('person_type', \App\Http\Requests\ContactAccidentRequest::$PersonTypes, array_search('Undergraduate', \App\Http\Requests\ContactAccidentRequest::$PersonTypes), ['class' => 'form-control']) !!}
                            {!! Form::text('person_type_other', null, ['class' => 'form-control', 'placeholder' => 'If other, please specify', 'style' => 'margin-bottom:0.2em;margin-top:0.7em;']) !!}
                            @include('partials.form.input-error', ['name' => 'person_type'])
                            @include('partials.form.input-error', ['name' => 'person_type_other'])
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Contact Details</legend>

                    <!-- Name -->
                    <div class="form-group @include('partials.form.error-class', ['name' => 'contact_name'])">
                        {!! Form::label('contact_name', 'Name:', ['class' => 'control-label col-sm-3']) !!}
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-user"></span></span>
                                {!! Form::text('contact_name', null, ['class' => 'form-control', 'placeholder' => 'The name of the contact']) !!}
                            </div>
                            @include('partials.form.input-error', ['name' => 'contact_name'])
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group @include('partials.form.error-class', ['name' => 'contact_email'])">
                        {!! Form::label('contact_email', 'Email:', ['class' => 'control-label col-sm-3']) !!}
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-at"></span></span>
                                {!! Form::text('contact_email', null, ['class' => 'form-control', 'placeholder' => 'Their email address']) !!}
                            </div>
                            @include('partials.form.input-error', ['name' => 'contact_email'])
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="form-group @include('partials.form.error-class', ['name' => 'contact_phone'])">
                        {!! Form::label('contact_phone', 'Phone:', ['class' => 'control-label col-sm-3']) !!}
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-phone"></span></span>
                                {!! Form::text('contact_phone', null, ['class' => 'form-control', 'placeholder' => 'Their phone number']) !!}
                            </div>
                            @include('partials.form.input-error', ['name' => 'contact_phone'])
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

        <div class="text-center" style="margin-top: 1em;">
            <button class="btn btn-success" disable-submit="Sending report ..." type="submit">
                <span class="fa fa-send"></span>
                <span>Send accident report</span>
            </button>
            <a class="btn btn-danger" href="{{ route('home') }}">
                <span class="fa fa-undo"></span>
                <span>Cancel</span>
            </a>
        </div>
    {!! Form::close() !!}
@endsection