@extends('app')

@section('title', 'Reset Your Password')

@section('content')
	<h1 class="page-header">Reset Your Password</h1>
    <p>If you have forgotten the password for your Backstage account, simply enter your email address into the field below. You will be sent a code which will
        allow you to change your password.</p>

    {!! Form::open(['style' => 'max-width:450px;']) !!}
        @if (Session::get('status'))
            <div class="alert alert-info form-error">
                <span class="fa fa-info"></span>
                <span>{{ Session::get('status') }}</span>
            </div>
        @endif

        <div class="form-group @if ($errors->default->has('email')) has-error @endif">
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-envelope"></span></span>
                {!! Form::text('email', null, [
                    'placeholder' => 'Enter your email address',
                    'class' => 'form-control'
                ]) !!}
            </div>
            @include('partials.form.input-error', ['name' => 'email'])
        </div>

    <div class="form-group">
        <div class="btn-group">
            <button class="btn btn-success" disable-submit="Sending email ..." type="submit">
                <span class="fa fa-send"></span>
                <span>Send Reset Link</span>
            </button>
            <a class="btn btn-danger" href="{{ route('auth.login') }}">
                <span class="fa fa-long-arrow-left"></span>
                <span>Cancel</span>
            </a>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
