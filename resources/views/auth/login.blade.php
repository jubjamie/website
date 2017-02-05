@extends('app.main')

@section('title', 'Log In')
@section('header-main', 'Log In')
@section('page-section', 'auth')
@section('page-id', 'login')

@section('content')
    <p>To access the members' area you need a username and password; these are provided once you have attended our induction. If you have attended this
        induction but have not received your log in details please <a href="mailto:sec@bts-crew.com">contact the secretary</a>.</p>

    {!! Form::open() !!}

    <div class="form-group @InputClass('username')">
        <div class="input-group">
            <span class="input-group-addon"><span class="fa fa-user"></span></span>
            {!! Form::text('username', null, [
                'placeholder' => 'Enter your username or email address',
                'class' => 'form-control'
            ]) !!}
        </div>
        @InputError('username')
    </div>

    <div class="form-group @InputClass('password')">
        <div class="input-group">
            <span class="input-group-addon"><span class="fa fa-key"></span></span>
            {!! Form::input('password', 'password', null, [
                'placeholder' => 'Enter your password',
                'class' => 'form-control'
            ]) !!}
        </div>
        @InputError('password')
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
            <a class="btn btn-primary" href="{{ route('auth.pwd.email') }}">
                <span class="fa fa-unlock-alt"></span>
                <span>Reset your password</span>
            </a>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
