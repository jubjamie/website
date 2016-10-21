@extends('contact.shared')

@section('title', 'Enquiries')

@section('tab')
    <p>If you have a general question for Backstage then you can use the form below to send us an email; alternatively you are welcome to pop over to our office
        (<em class="bts">1E 3.4</em>) or call us on <em class="bts">01225 383067</em>.</p>
    <p>Please do not use this to submit a booking request; use the {!! link_to_route('contact.book', 'booking request form') !!} instead.</p>
    {!! Form::open(['style' => 'max-width:600px;']) !!}
        @include('partials.form.summary-errors')

        <!-- Text field for 'name' -->
        <div class="form-group @include('partials.form.error-class', ['name' => 'name'])">
            {!! Form::label('name', 'Your name:', ['class' => 'control-label']) !!}
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-user"></span></span>
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'John Smith']) !!}
            </div>
            @include('partials.form.input-error', ['name' => 'name'])
        </div>

        <!-- Email field for 'email' -->
        <div class="form-group @include('partials.form.error-class', ['name' => 'email'])">
            {!! Form::label('email', 'Your email address:', ['class' => 'control-label']) !!}
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-at"></span></span>
                {!! Form::input('email', 'email', null, ['class' => 'form-control', 'placeholder' => 'abc123@bath.ac.uk']) !!}
            </div>
            @include('partials.form.input-error', ['name' => 'email'])
        </div>

        <!-- Text field for 'phone' -->
        <div class="form-group @include('partials.form.error-class', ['name' => 'phone'])">
            {!! Form::label('phone', 'Your phone number (optional):', ['class' => 'control-label']) !!}
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-phone"></span></span>
                {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => '(this is optional)']) !!}
            </div>
            @include('partials.form.input-error', ['name' => 'phone'])
        </div>

        <!-- Textarea for 'message' -->
        <div class="form-group @include('partials.form.error-class', ['name' => 'message'])">
            {!! Form::label('message', 'Your message or question:', ['class' => 'control-label']) !!}
            {!! Form::textarea('message', null, ['class' => 'form-control', 'placeholder' => 'We\'ll make sure this gets forwarded to the appropriate person', 'rows' => 6]) !!}
            @include('partials.form.input-error', ['name' => 'message'])
        </div>

        <div class="form-group @include('partials.form.error-class', ['name' => 'g-recaptcha-response'])">
            {!! Recaptcha::render() !!}
            @include('partials.form.input-error', ['name' => 'g-recaptcha-response'])
        </div>

        <div class="form-group">
            <button class="btn btn-success" disable-submit="Sending enquiry ..." type="submit">
                <span class="fa fa-send"></span>
                <span>Send Enquiry</span>
            </button>
            <a class="btn btn-danger" href="{{ route('home') }}">
                <span class="fa fa-undo"></span>
                <span>Cancel</span>
            </a>
        </div>
    {!! Form::close() !!}
@endsection