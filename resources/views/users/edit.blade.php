@extends('app')

@section('title', 'Edit User')

@section('stylesheets')
    @include('partials.tags.style', ['path' => 'partials/users'])
@endsection

@if($ownAccount)
@section('messages')
    @include('partials.flash.message', ['title' => 'Editing restricted', 'message' => 'You are editing your own account so some things are restricted', 'level' => 'warning'])
@endsection
@endif

@section('content')
    <h1 class="page-header">@yield('title')</h1>
    <div id="editUser">
        {!! Form::model($user, ['class' => 'form-horizontal', 'route' => ['user.edit.do', $user->username]]) !!}
        <div class="row">
            <div class="col-md-8">
                <fieldset>
                    <legend>Personal Details</legend>
                    {{-- Name --}}
                    <div class="form-group @include('partials.form.error-class', ['name' => 'name'])">
                        {!! Form::label('name', 'Name:', ['class' => 'control-label col-sm-3']) !!}
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-user"></span></span>
                                {!! Form::text('name', null, ['class' => 'form-control']) !!}
                            </div>
                            @include('partials.form.input-error', ['name' => 'name'])
                        </div>
                    </div>

                    {{-- Username --}}
                    <div class="form-group @include('partials.form.error-class', ['name' => 'username'])">
                        {!! Form::label('username', 'Username:', ['class' => 'control-label col-sm-3']) !!}
                        <div class="col-sm-9">
                            @if($ownAccount)
                                <p class="form-control-static">{{ $user->username }}</p>
                            @else
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-user"></span></span>
                                    {!! Form::text('username', null, ['class' => 'form-control']) !!}
                                </div>
                                @include('partials.form.input-error', ['name' => 'username'])
                            @endif
                        </div>
                    </div>

                    {{-- Nickname --}}
                    <div class="form-group @include('partials.form.error-class', ['name' => 'nickname'])">
                        {!! Form::label('name', 'Nickname:', ['class' => 'control-label col-sm-3']) !!}
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-user"></span></span>
                                {!! Form::text('nickname', null, ['class' => 'form-control']) !!}
                            </div>
                            @include('partials.form.input-error', ['name' => 'nickname'])
                        </div>
                    </div>

                    {{-- Email address --}}
                    <div class="form-group @include('partials.form.error-class', ['name' => 'email'])">
                        {!! Form::label('email', 'Email Address:', ['class' => 'control-label col-sm-3']) !!}
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-at"></span></span>
                                {!! Form::input('email', 'email', null, ['class' => 'form-control']) !!}
                            </div>
                            @include('partials.form.input-error', ['name' => 'email'])
                        </div>
                    </div>

                    {{-- Phone number --}}
                    <div class="form-group @include('partials.form.error-class', ['name' => 'phone'])">
                        {!! Form::label('phone', 'Phone Number:', ['class' => 'control-label col-sm-3']) !!}
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-phone"></span></span>
                                {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => 'This will only be shared if you allow it']) !!}
                            </div>
                            @include('partials.form.input-error', ['name' => 'phone'])
                        </div>
                    </div>

                    {{-- Date of Birth --}}
                    <div class="form-group @include('partials.form.error-class', ['name' => 'dob'])">
                        {!! Form::label('dob', 'Date of Birth:', ['class' => 'control-label col-sm-3']) !!}
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                {!! Form::text('dob', $user->dob ? $user->dob->format('Y-m-d') : '', ['class' => 'form-control', 'placeholder' => 'yyyy-mm-dd']) !!}
                            </div>
                            @include('partials.form.input-error', ['name' => 'dob'])
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="form-group @include('partials.form.error-class', ['name' => 'address'])">
                        {!! Form::label('address', 'Address:', ['class' => 'control-label col-sm-3']) !!}
                        <div class="col-sm-9">
                            <div class="input-group textarea">
                                <span class="input-group-addon"><span class="fa fa-home"></span></span>
                                {!! Form::textarea('address', null, ['class' => 'form-control resize-y', 'rows' => 4]) !!}
                            </div>
                            @include('partials.form.input-error', ['name' => 'address'])
                        </div>
                    </div>

                    {{-- Tool Colours --}}
                    <div class="form-group @include('partials.form.error-class', ['name' => 'tool_colours'])">
                        {!! Form::label('tool_colours', 'Tool Colours:', ['class' => 'control-label col-sm-3']) !!}
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-wrench"></span></span>
                                {!! Form::text('tool_colours', null, ['class' => 'form-control']) !!}
                            </div>
                            @include('partials.form.input-error', ['name' => 'tool_colours'])
                        </div>
                    </div>

                    {{-- Account type --}}
                    <div class="form-group @include('partials.form.error-class', ['name' => 'type'])">
                        {!! Form::label('type', 'Account Type:', ['class' => 'control-label col-sm-3']) !!}
                        <div class="col-sm-9">
                            @if($ownAccount)
                                <p class="form-control-static">{{ \App\User::$CreateAccountTypes[$user->type] }}</p>
                            @else
                                {!! Form::select('type', \App\User::$EditAccountTypes, null, ['class' => 'form-control']) !!}
                                @include('partials.form.input-error', ['name' => 'type'])
                            @endif
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="form-group">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-9">
                            <div class="btn-group">
                                <button class="btn btn-success" disable-submit="Saving changes ..." name="action" value="save">
                                    <span class="fa fa-check"></span>
                                    <span>Save Changes</span>
                                </button>
                                <button class="btn btn-primary" disable-submit="Sending password ..." name="action" value="reset-password">
                                    <span class="fa fa-key"></span>
                                    <span>Reset password</span>
                                </button>
                                @if(!$ownAccount)
                                    @if($user->status)
                                        <button class="btn btn-warning" disable-submit="Archiving ..." name="action" value="archive">
                                            <span class="fa fa-archive"></span>
                                            <span>Archive</span>
                                        </button>
                                    @else
                                        <button class="btn btn-success" disable-submit="Unarchiving ..." name="action" value="unarchive">
                                            <span class="fa fa-archive"></span>
                                            <span>Unarchive</span>
                                        </button>
                                    @endif
                                @endif
                                <a class="btn btn-danger" href="{{ route('user.index') }}">
                                    <span class="fa fa-undo"></span>
                                    <span>Cancel</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="col-md-4">
                <fieldset class="privacy">
                    <legend>Privacy Settings</legend>
                    <p>Let Backstage members see their:</p>
                    <div class="form-group @include('partials.form.error-class', ['name' => 'privacy'])">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('show_email', true) !!}
                                Email address
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('show_phone', true) !!}
                                Phone number
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('show_address', true) !!}
                                Address
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('show_age', true) !!}
                                Age
                            </label>
                        </div>
                    </div>
                </fieldset>
                @include('users._profile_pic')
            </div>
        </div>
        {!! Form::close() !!}
    </div>
@endsection

@section('modal')
    @include('users.modal.profile_pic')
@endsection