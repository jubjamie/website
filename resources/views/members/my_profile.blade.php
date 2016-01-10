@extends('app')

@section('title', 'My Profile')

@section('stylesheets')
    @include('partials.tags.style', ['path' => 'partials/members'])
    @include('partials.tags.style', ['path' => 'partials/events'])
    @include('partials.tags.style', ['path' => 'partials/training'])
@endsection

@section('scripts')
    $('#profileTab').tabify();
@endsection

@section('content')
    <h1 class="page-header">My Profile</h1>
    <div id="myProfile">
        @if($user->isMember())
            <div class="tabpanel" id="profileTab">
                {!! $menu !!}
                <div class="tab-content">
                    <div class="tab-pane{{ $tab == 'profile' ? ' active' : '' }}">
                        @include('members.partials.profile', ['user' => $user])
                    </div>
                    <div class="tab-pane{{ $tab == 'events' ? ' active' : '' }}">
                        @include('members.partials.events', ['user' => $user])
                    </div>
                    <div class="tab-pane{{ $tab == 'training' ? ' active' : '' }}">
                        @include('members.partials.skills', ['user' => $user])
                    </div>
                </div>
            </div>
        @else
            @include('members.partials.profile', ['user' => $user])
        @endif
    </div>
@endsection

@section('modal')
    @if($user->isMember())
    @include('users.modal.profile_pic', ['ownProfile' => true])
    @endif
    <div data-type="modal-template" data-id="password">
        <div class="modal-header">
            <h1>Change your password</h1>
        </div>
        {!! Form::open() !!}
        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('password', 'Password:', ['class' => 'control-label']) !!}
                {!! Form::password('password', ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('password_confirmation', 'Confirm:', ['class' => 'control-label']) !!}
                {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-success" data-type="submit-modal" data-form-action="{{ route('members.myprofile.password') }}" type="button">
                <span class="fa fa-check"></span>
                <span>Update</span>
            </button>
        </div>
        {!! Form::close() !!}
    </div>
    <div data-type="data-toggle-template" data-toggle-id="privacy" data-value="true">
        @include('members.partials.privacy_enabled')
    </div>
    <div data-type="data-toggle-template" data-toggle-id="privacy" data-value="false">
        @include('members.partials.privacy_disabled')
    </div>
@endsection