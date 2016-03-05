@extends('app')
@section('page-section', 'members events training')
@section('title', $user->getPossessiveName("Profile"))

@section('scripts')
    $('#profileTab').tabify();
@endsection

@if($user->id == Auth::user()->id)
@section('messages')
    @include('partials.flash.message', ['title' => 'Viewing public profile', 'message' => 'You are viewing your profile as it would appear to another member.' . PHP_EOL . 'To view all of your profile, and make changes, <a href="' . route('members.myprofile') . '">go to your profile</a>.', 'level' => 'info', 'perm' => true])
@endsection
@endif

@section('content')
    <div id="viewProfile">
        <div class="row header">
            <div class="col-sm-5 hidden-xs text-right">
                <img class="img-rounded" src="{{ $user->getAvatarUrl() }}">
            </div>
            <div class="col-sm-7">
                <h1>{{ $user->nickname ? sprintf('%s "%s" %s', $user->forename, $user->nickname, $user->surname) : $user->name }}</h1>

                <h3>{{ $user->username }}</h3>
            </div>
        </div>
        <div class="tabpanel" id="profileTab">
            {!! $menu !!}
            <div class="tab-content">
                <div class="tab-pane{{ $tab == 'profile' ? ' active' : '' }}">
                    <div style="margin:0 auto;max-width: 500px;">
                        <div class="row">
                            <div class="col-sm-6 box">
                                <h1>Member Details</h1>
                                <table>
                                    <tr>
                                        <td style="width: 7.1em;">Active:</td>
                                        <td><span class="fa-lg fa fa-{{ $user->status ? 'check success' : 'remove danger' }}"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Committee:</td>
                                        <td><span class="fa-lg fa fa-{{ $user->isCommittee() ? 'check success' : 'remove danger' }}"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Associate:</td>
                                        <td><span class="fa-lg fa fa-{{ $user->isAssociate() ? 'check success' : 'remove danger' }}"></span></td>
                                    </tr>
                                    @if($user->tool_colours)
                                        <tr>
                                            <td>Tool Colours:</td>
                                            <td>{!! $user->getToolColours() !!}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-sm-6 box">
                                <h1>Contact Details</h1>
                                @if($user->show_email || $user->show_phone || $user->show_address)
                                <table>
                                    @if($user->show_email && $user->email)
                                    <tr>
                                        <td><span class="fa fa-envelope"></span></td>
                                        <td>{!! link_to('mailto:' . $user->email, $user->email) !!}</td>
                                    </tr>
                                    @endif
                                    @if($user->show_phone && $user->phone)
                                    <tr>
                                        <td><span class="fa fa-phone"></span></td>
                                        <td>{{ $user->phone }}</td>
                                    </tr>
                                    @endif
                                    @if($user->show_address && $user->address)
                                    <tr>
                                        <td><span class="fa fa-home"></span></td>
                                        <td>{!! nl2br($user->address) !!}</td>
                                    </tr>
                                    @endif
                                </table>
                                @else
                                    <h4>{{ $user->forename }} hasn't shared any of their contact details</h4>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane{{ $tab == 'events' ? ' active' : '' }}">
                    @include('members.partials.events', ['user' => $user])
                </div>
                <div class="tab-pane{{ $tab == 'training' ? ' active' : '' }}">
                    @include('members.partials.skills', ['user' => $user])
                </div>
            </div>
        </div>
    </div>
@endsection