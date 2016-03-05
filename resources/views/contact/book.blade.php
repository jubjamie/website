@extends('contact.shared')

@section('title', 'Book Us')

@section('styles')
    @media (min-width: 992px) { div.col-md-6:nth-child(even) { margin-left: 9px; width: 49%; }}
@endsection

@section('scripts')
    $modal.on('click', '#btnAcceptTerms', function() {
        $modal.modal('hide');
        $('input[name="terms"]').attr('checked', 'checked');
    });
@endsection

@section('tab')
    <p>Please use the following form if you wish to request a quote or enquire about booking Backstage. You will receive an acknowledgement of your request but please note that this is not a confirmation that Backstage will crew your event. The Production Manager will get back to you with a definite answer as soon as possible.</p>

    {!! Form::open() !!}
        @include('partials.form.summary-errors')

        <div class="row">
            <div class="col-md-6">
                <fieldset class="row">
                    <legend>Event Details</legend>

                    <div class="form-group @include('partials.form.error-class', ['name' => 'event_name'])">
                        {!! Form::label('event_name', 'Name:', ['class' => 'control-label']) !!}
                        <div class="input-group">
                            <span class="input-group-addon"><span class="fa fa-edit"></span></span>
                            {!! Form::text('event_name', null, ['class' => 'form-control', 'placeholder' => 'What\'s it called?']) !!}
                        </div>
                        @include('partials.form.input-error', ['name' => 'event_name'])
                    </div>

                    <div class="form-group @include('partials.form.error-class', ['name' => 'event_venue'])">
                        {!! Form::label('event_venue', 'Venue:', ['class' => 'control-label']) !!}
                        <div class="input-group">
                            <span class="input-group-addon"><span class="fa fa-home"></span></span>
                            {!! Form::text('event_venue', null, ['class' => 'form-control', 'placeholder' => 'Where is it?']) !!}
                        </div>
                        @include('partials.form.input-error', ['name' => 'event_venue'])
                    </div>

                    <div class="form-group @include('partials.form.error-class', ['name' => 'event_description'])">
                        {!! Form::label('event_description', 'Description:', ['class' => 'control-label']) !!}
                        {!! Form::textarea('event_description', null, ['class' => 'form-control', 'placeholder' => 'Briefly describe what the event is about', 'rows' => 5]) !!}
                        @include('partials.form.input-error', ['name' => 'event_description'])
                    </div>

                    <div class="form-group @include('partials.form.error-class', ['name' => 'event_dates'])">
                        {!! Form::label('event_dates', 'Event Date(s):', ['class' => 'control-label']) !!}
                        <div class="input-group">
                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                            {!! Form::text('event_dates', null, ['class' => 'form-control', 'placeholder' => 'When are the shows?']) !!}
                        </div>
                        @include('partials.form.input-error', ['name' => 'event_dates'])
                    </div>

                    <div class="form-group @include('partials.form.error-class', ['name' => 'show_time'])">
                        {!! Form::label('show_time', 'Show Time:', ['class' => 'control-label']) !!}
                        <div class="input-group">
                            <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                            {!! Form::text('show_time', null, ['class' => 'form-control', 'placeholder' => '(this is optional)']) !!}
                        </div>
                        @include('partials.form.input-error', ['name' => 'show_time'])
                    </div>

                    <div class="form-group no-highlight @include('partials.form.error-class', ['name' => 'event_access'])">
                        {!! Form::label('event_access', 'When can we get into the venue?', ['class' => 'control-label']) !!}
                        <div class="checkbox">
                            <label class="checkbox-inline">
                                {!! Form::checkbox('event_access[]', 0, null) !!}
                                Morning
                            </label>
                            <label class="checkbox-inline">
                                {!! Form::checkbox('event_access[]', 1, null) !!}
                                Afternoon
                            </label>
                            <label class="checkbox-inline">
                                {!! Form::checkbox('event_access[]', 2, null) !!}
                                Evening
                            </label>
                        </div>
                        @include('partials.form.input-error', ['name' => 'event_access'])
                    </div>
                </fieldset>
            </div>
            <div class="col-md-6">
                <fieldset class="row">
                    <legend>Contact Details</legend>

                    <div class="form-group @include('partials.form.error-class', ['name' => 'event_club'])">
                        {!! Form::label('event_club', 'Club / organisation:', ['class' => 'control-label']) !!}
                        <div class="input-group">
                            <span class="input-group-addon"><span class="fa fa-group"></span></span>
                            {!! Form::text('event_club', null, ['class' => 'form-control', 'placeholder' => 'Who is this for?']) !!}
                        </div>
                        @include('partials.form.input-error', ['name' => 'event_club'])
                    </div>

                    <div class="form-group @include('partials.form.error-class', ['name' => 'contact_name'])">
                        {!! Form::label('contact_name', 'Contact Person:', ['class' => 'control-label']) !!}
                        <div class="input-group">
                            <span class="input-group-addon"><span class="fa fa-user"></span></span>
                            {!! Form::text('contact_name', null, ['class' => 'form-control', 'placeholder' => 'Who we\'ll be talking to']) !!}
                        </div>
                        @include('partials.form.input-error', ['name' => 'contact_name'])
                    </div>

                    <div class="form-group @include('partials.form.error-class', ['name' => 'contact_email'])">
                        {!! Form::label('contact_email', 'Contact Email:', ['class' => 'control-label']) !!}
                        <div class="input-group">
                            <span class="input-group-addon"><span class="fa fa-at"></span></span>
                            {!! Form::input('email', 'contact_email', null, ['class' => 'form-control', 'placeholder' => 'We\'ll use this to discuss your booking']) !!}
                        </div>
                        @include('partials.form.input-error', ['name' => 'contact_email'])
                    </div>

                    <div class="form-group @include('partials.form.error-class', ['name' => 'contact_phone'])">
                        {!! Form::label('contact_phone', 'Contact Phone (optional):', ['class' => 'control-label']) !!}
                        <div class="input-group">
                            <span class="input-group-addon"><span class="fa fa-phone"></span></span>
                            {!! Form::text('contact_phone', null, ['class' => 'form-control', 'placeholder' => '(this is optional)']) !!}
                        </div>
                        @include('partials.form.input-error', ['name' => 'contact_phone'])
                    </div>
                </fieldset>

                <fieldset class="row" style="padding-bottom: 0.9em;">
                    <legend>Additional Info</legend>
                    <div class="form-group @include('partials.form.error-class', ['name' => 'additional'])">
                        {!! Form::label('additional', 'Please include any additional requests you may have', ['class' => 'control-label']) !!}
                        {!! Form::textarea('additional', null, ['class' => 'form-control', 'placeholder' => '', 'rows' => 5]) !!}
                        @include('partials.form.input-error', ['name' => 'additional'])
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="row text-center" style="margin-top: 1em;">
            <div class="form-group @include('partials.form.error-class', ['name' => 'g-recaptcha-response'])">
                @include('partials.form.input-error', ['name' => 'g-recaptcha-response'])
                <div class="g-recaptcha-center">
                    {!! Recaptcha::render() !!}
                </div>
            </div>
            <div class="form-group @include('partials.form.error-class', ['name' => 'terms'])">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('terms', 1, null) !!}
                        I agree to the <a href="{{ route('contact.book.terms') }}"
                                          id="show_terms"
                                          data-toggle="modal"
                                          data-target="#modal"
                                          data-modal-template="terms"
                                          data-modal-title="Terms and Conditions for the Provision of Services"
                                          target="_blank">Terms and Conditions</a>.
                    </label>
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-success" disable-submit="Submitting booking ..." type="submit">
                    <span class="fa fa-send"></span>
                    <span>Submit booking request</span>
                </button>
                <a class="btn btn-danger" href="{{ route('home') }}">
                    <span class="fa fa-undo"></span>
                    <span>Cancel</span>
                </a>
            </div>
        </div>
    {!! Form::close() !!}
@endsection

@section('modal')
    <div data-type="modal-template" data-id="terms">
        <div class="modal-body">
            @include('contact._book_terms')
        </div>
        <div class="modal-footer text-center">
            <button class="btn btn-success" id="btnAcceptTerms">
                <span class="fa fa-thumbs-up"></span>
                <span>I have read and accept these terms</span>
            </button>
        </div>
    </div>
@endsection