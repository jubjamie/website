@extends('app')

@section('title', 'Log In')

@section('content')
        <h1 class="page-header">@yield('title')</h1>
        <p>To access the members' area you need a username and password; these are provided once you have attended our induction. If you have attended this
            induction but have not received your log in details please <a href="mailto:sec@bts-crew.com">contact the secretary</a>.</p>

    {!! Form::open(['style' => 'margin-top:2.5em;max-width:400px;']) !!}

        @include('partials.form.summary-errors')

        <div class="form-group @if ($errors->default->has('username')) has-error @endif">
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-user"></span></span>
                {!! Form::text('username', null, [
                    'placeholder' => 'Enter your username or email address',
                    'class' => 'form-control'
                ]) !!}
            </div>
            @include('partials.form.input-error', ['name' => 'username'])
        </div>

        <div class="form-group @if ($errors->default->has('password')) has-error @endif">
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-key"></span></span>
                {!! Form::input('password', 'password', null, [
                    'placeholder' => 'Enter your password',
                    'class' => 'form-control'
                ]) !!}
            </div>
            @include('partials.form.input-error', ['name' => 'password'])
        </div>

        <div class="form-group">
            <div class="checkbox">
                <label>
                    <input name="remember" type="checkbox" value="1">
                    Remember me
                </label>
            </div>
        </div>

        <div class="form-group">
            <div class="btn-group">
                <button class="btn btn-success" disable-submit="Logging in ..." type="submit">
                    <span class="fa fa-sign-in"></span>
                    <span>Log in</span>
                </button>
                <a class="btn btn-primary" href="{{ route('pwd.email') }}">
                    <span class="fa fa-unlock-alt"></span>
                    <span>Reset your password</span>
                </a>
            </div>
        </div>
    {!! Form::close() !!}
@endsection
