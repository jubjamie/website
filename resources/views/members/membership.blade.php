@extends('app')

@section('title', 'The Membership')

@section('stylesheets')
    @include('partials.tags.style', ['path' => 'partials/members'])
@endsection

@section('content')
    <h1 class="page-header">The Membership</h1>
    <div id="viewMembership">
        @if(count($members) > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th class="pic hidden-xs hidden-sm"></th>
                        <th class="name">Name</th>
                        <th class="email">Email Address</th>
                        <th class="phone text-center hidden-xs">Phone</th>
                        <th class="tool text-center hidden-xs">Tools</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $member)
                        <tr onclick="document.location='{{ route('members.profile', $member->username) }}';">
                            <td class="pic hidden-xs hidden-sm">
                                <img class="img-circle" src="{{ $member->getAvatarUrl() }}">
                            </td>
                            <td class="name">
                                {!! link_to_route('members.profile', $member->name, [$member->username]) !!}
                            </td>
                            <td class="email">
                                @if($member->show_email)
                                    <a href="mailto:{{ $member->email }}">{{ $member->email }}</a>
                                @else
                                    <em>- hidden -</em>
                                @endif
                            </td>
                            <td class="phone text-center hidden-xs">
                                @if($member->show_phone)
                                    {{ $member->phone }}
                                @else
                                    <em>- hidden -</em>
                                @endif
                            </td>
                            <td class="tool text-center hidden-xs">
                                {!! $member->getToolColours() !!}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if(Auth::user()->can('admin'))
                <a class="btn btn-success" href="{{ route('user.create') }}">
                    <span class="fa fa-user-plus"></span>
                    <span>Add more users</span>
                </a>
                <a class="btn btn-primary" href="{{ route('user.index') }}">
                    <span class="fa fa-list"></span>
                    <span>View all users</span>
                </a>
            @endif
        @else
            <h3 class="no-members">Well this is awkward ... we don't seem to have any members</h3>
        @endif
    </div>
@endsection