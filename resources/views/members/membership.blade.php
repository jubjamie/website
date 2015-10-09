@extends('app')

@section('title', 'The Membership')

@section('stylesheets')
    @include('partials.tags.style', ['path' => 'partials/members'])
@endsection

@section('content')
    <h1 class="page-header">The Membership</h1>
    <div id="viewMembership">
        <table class="table">
            <thead class="hidden-xs hidden-sm">
                <tr>
                    <th class="pic"></th>
                    <th class="name">Name</th>
                    <th class="email">Email Address</th>
                    <th class="phone text-center">Phone</th>
                    <th class="tool text-center">Tools</th>
                </tr>
            </thead>
            <tbody>
                @if(count($members) > 0)
                @foreach($members as $member)
                    <tr onclick="document.location='{{ route('members.profile', $member->username) }}';">
                        <td class="pic">
                            <img class="img-circle" src="{{ $member->getAvatarUrl() }}">
                        </td>
                        <td class="name">
                            <div class="name">
                                <a href="{{ route('members.profile', $member->username) }}">
                                    {{ $member->name }}

                                    @if($member->nickname)
                                        <span class="nickname">
                                            ({{ $member->nickname }})
                                        </span>
                                    @endif
                                </a>
                            </div>
                            <div class="email visible-xs visible-sm">
                                @if($member->show_email)
                                    <a href="mailto:{{ $member->email }}">{{ $member->email }}</a>
                                @else
                                    <em>- hidden -</em>
                                @endif
                            </div>
                        </td>
                        <td class="email hidden-xs hidden-sm">
                            @if($member->show_email)
                                <a href="mailto:{{ $member->email }}">{{ $member->email }}</a>
                            @else
                                <em>- hidden -</em>
                            @endif
                        </td>
                        <td class="phone text-center hidden-xs hidden-sm">
                            @if($member->show_phone)
                                {{ $member->phone }}
                            @else
                                <em>- hidden -</em>
                            @endif
                        </td>
                        <td class="tool text-center">
                            {!! $member->getToolColours() !!}
                        </td>
                    </tr>
                @endforeach
                @else
                    <tr>
                        <td colspan="5">No members matched your search query</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div>
            @if(Auth::user()->can('admin'))
                <div class="btn-group">
                    <a class="btn btn-success" href="{{ route('user.create') }}">
                        <span class="fa fa-user-plus"></span>
                        <span>Add more users</span>
                    </a>
                    <a class="btn btn-primary" href="{{ route('user.index') }}">
                        <span class="fa fa-list"></span>
                        <span>View all users</span>
                    </a>
                </div>
            @endif
                <div class="pull-right">
                    <input class="form-control input-sm search-input"
                           data-type="search-input"
                           data-url-base="{{ route('membership') }}"
                           placeholder="Search ..."
                           type="text"
                           value="{{ $search ?: '' }}">
                </div>
        </div>
    </div>
@endsection