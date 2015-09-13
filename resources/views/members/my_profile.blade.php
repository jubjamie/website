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
    <div id="viewProfile">
        <div class="tabpanel" id="profileTab">
            {!! $menu !!}
            <div class="tab-content">
                <div class="tab-pane active">
                    {!! Form::open(['class' => 'form-horizontal']) !!}
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-8">
                                <fieldset>
                                    <legend>My Details</legend>
                                    {{-- Name --}}
                                    <div class="form-group">
                                        {!! Form::label('name', 'My name:', ['class' => 'col-md-4 control-label']) !!}
                                        <div class="col-md-8">
                                            <p class="form-control-static"
                                               data-editable="true"
                                               data-edit-type="text"
                                               data-control-name="name"
                                               role="button">{{ $user->name }}</p>
                                        </div>
                                    </div>
                                    {{-- Email --}}
                                    <div class="form-group">
                                        {!! Form::label('email', 'Email address:', ['class' => 'col-md-4 control-label']) !!}
                                        <div class="col-md-8">
                                            <p class="form-control-static"
                                               data-editable="true"
                                               data-edit-type="text"
                                               data-control-name="email"
                                               role="button">{{ $user->email }}</p>
                                            <span class="toggle"
                                                  data-editable="true"
                                                  data-edit-type="toggle"
                                                  data-key="show_email"
                                                  data-value="{{ $user->show_email }}"
                                                  data-toggle-template="privacy"
                                                  data-edit-url="{{ route('members.myprofile.do') }}"
                                                  role="button">
                                                @if($user->show_email)
                                                    @include('members.partials.privacy_enabled')
                                                @else
                                                    @include('members.partials.privacy_disabled')
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    {{-- DOB --}}
                                    <div class="form-group">
                                        {!! Form::label('dob', 'Date of Birth:', ['class' => 'col-md-4 control-label']) !!}
                                        <div class="col-md-8">
                                            <p class="form-control-static"
                                               data-editable="true"
                                               data-edit-type="text"
                                               data-control-name="dob"
                                               data-config="{{ json_encode(['text' => ['' => '- not set -']]) }}"
                                               role="button">{{ $user->dob ? $user->dob->format('d/m/Y') : '- not set -' }}</p>
                                             <span class="toggle"
                                                   data-editable="true"
                                                   data-edit-type="toggle"
                                                   data-key="show_age"
                                                   data-value="{{ $user->show_age }}"
                                                   data-toggle-template="privacy"
                                                   data-edit-url="{{ route('members.myprofile.do') }}"
                                                   role="button">
                                                @if($user->show_age)
                                                     @include('members.partials.privacy_enabled')
                                                 @else
                                                     @include('members.partials.privacy_disabled')
                                                 @endif
                                            </span>
                                        </div>
                                    </div>
                                    {{-- Phone --}}
                                    <div class="form-group">
                                        {!! Form::label('phone', 'Phone number:', ['class' => 'col-md-4 control-label']) !!}
                                        <div class="col-md-8">
                                            <p class="form-control-static"
                                               data-editable="true"
                                               data-edit-type="text"
                                               data-control-name="phone"
                                               data-config="{{ json_encode(['text' => ['' => '- not set -']]) }}"
                                               role="button">{{ $user->phone ?: '- not set -' }}</p>
                                            <span class="toggle"
                                                  data-editable="true"
                                                  data-edit-type="toggle"
                                                  data-key="show_phone"
                                                  data-value="{{ $user->show_phone }}"
                                                  data-toggle-template="privacy"
                                                  data-edit-url="{{ route('members.myprofile.do') }}"
                                                  role="button">
                                                @if($user->show_phone)
                                                    @include('members.partials.privacy_enabled')
                                                @else
                                                    @include('members.partials.privacy_disabled')
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    {{-- Address --}}
                                    <div class="form-group">
                                        {!! Form::label('address', 'Address:', ['class' => 'col-md-4 control-label']) !!}
                                        <div class="col-md-8">
                                            <p class="form-control-static"
                                               data-editable="true"
                                               data-edit-type="textarea"
                                               data-control-name="address"
                                               data-config="{{ json_encode(['text' => ['' => '- not set -']]) }}"
                                               role="button">{!! $user->address ? nl2br($user->address) : '- not set -'!!}</p>
                                            <span class="toggle"
                                                  data-editable="true"
                                                  data-edit-type="toggle"
                                                  data-key="show_address"
                                                  data-value="{{ $user->show_address }}"
                                                  data-toggle-template="privacy"
                                                  data-edit-url="{{ route('members.myprofile.do') }}"
                                                  role="button">
                                                @if($user->show_address)
                                                    @include('members.partials.privacy_enabled')
                                                @else
                                                    @include('members.partials.privacy_disabled')
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    {{-- Tools --}}
                                    <div class="form-group">
                                        {!! Form::label('tool_colours', 'Tool Colours:', ['class' => 'col-md-4 control-label']) !!}
                                        <div class="col-md-8">
                                            <p class="form-control-static"
                                               data-editable="true"
                                               data-edit-type="text"
                                               data-control-name="tool_colours"
                                               data-config="{{ json_encode(['text' => ['' => '- not set -']]) }}"
                                               role="button">{{ $user->tool_colours ?: '- not set -' }}</p>
                                        </div>
                                    </div>
                                    {{-- Buttons --}}
                                    <div class="form-group">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-8">
                                            <a class="btn btn-primary" href="{{ route('members.profile', $user->username) }}" target="_blank">
                                                <span class="fa fa-external-link"></span>
                                                <span>See what this looks like</span>
                                            </a>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-4">
                                @include('users._profile_pic')
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="tab-pane">
                    @include('members.partials.events', ['user' => $user])
                </div>
                <div class="tab-pane">
                    @include('members.partials.skills', ['user' => $user])
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('users.modal.profile_pic', ['ownProfile' => true])
    <div data-type="data-toggle-template" data-toggle-id="privacy" data-value="true">
        @include('members.partials.privacy_enabled')
    </div>
    <div data-type="data-toggle-template" data-toggle-id="privacy" data-value="false">
        @include('members.partials.privacy_disabled')
    </div>
@endsection