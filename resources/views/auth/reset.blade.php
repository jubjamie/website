@extends('app')

@section('title', 'Reset Your Password')

@section('content')
	<h1 class="page-header">Reset Your Password</h1>
    <p style="text-align: center;">To complete the process, confirm your email address and enter a new password.</p>
    {!! Form::open(['style' => 'max-width:450px;']) !!}
        <input type="hidden" name="token" value="{{ $token }}">

        @include('partials.form.summary-errors')

        <div class="form-group @if ($errors->default->has('email')) has-error @endif">
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-envelope"></span></span>
                {!! Form::text('email', null, [
                    'placeholder' => 'Confirm your email address',
                    'class' => 'form-control'
                ]) !!}
            </div>
            @include('partials.form.input-error', ['name' => 'email'])
        </div>

        <div class="form-group @if ($errors->default->has('password')) has-error @endif">
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-key"></span></span>
                {!! Form::input('password', 'password', null, [
                'placeholder' => 'Enter a new password',
                    'class' => 'form-control'
                ]) !!}
            </div>
            @include('partials.form.input-error', ['name' => 'password'])
        </div>

        <div class="form-group @if ($errors->default->has('password_confirmation')) has-error @endif">
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-key"></span></span>
                {!! Form::input('password', 'password_confirmation', null, [
                'placeholder' => 'Confirm your password',
                    'class' => 'form-control'
                ]) !!}
            </div>
            @include('partials.form.input-error', ['name' => 'password_confirmation'])
        </div>

        <div class="form-group">
            <div class="btn-group">
                <button class="btn btn-success" disable-submit="Resetting password ..." type="submit">
                    <span class="fa fa-check"></span>
                    <span>Reset Password</span>
                </button>
                <a class="btn btn-danger" href="{{ route('auth.login') }}">
                    <span class="fa fa-long-arrow-left"></span>
                    <span>Cancel</span>
                </a>
            </div>
        </div>
    {!! Form::close() !!}
@endsection
