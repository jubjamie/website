@extends('app')

@section('title', 'User Manager')

@section('stylesheets')
    @include('partials.tags.style', ['path' => 'partials/users'])
@endsection

@section('content')
    <h1>User Manager</h1>
    <p>This table lists all user accounts entered into the database, including non-members and archived accounts. To ensure that all past events display properly it is not possible to delete users; instead use the archive function to disable their account and remove them from any signup lists.</p>
    <a class="btn btn-success" href="{{ route('user.create') }}">
        <span class="fa fa-user-plus"></span>
        <span>Create a New User</span>
    </a>
    {!! Form::open(['route' => ['user.index.do'], 'id' => 'listUsers']) !!}
    <table class="table table-condensed">
        <thead>
            <tr>
                <th class="check">&nbsp;</th>
                <th class="id">&nbsp;</th>
                <th class="name">Name</th>
                <th class="username">Username</th>
                <th class="membership">Account Type</th>
                <th class="date">Registered</th>
                <th class="actions"></th>
            </tr>
        </thead>
        <tbody>
            @if($users->count() > 0)
                @foreach($users as $user)
                    <tr>
                        <td class="check">
                            {!! Form::checkbox('users[]', $user->id) !!}
                        </td>
                        <td class="id">{{ $user->id }}</td>
                        <td class="name">{{ $user->name }}</td>
                        <td class="username">{{ $user->username }}</td>
                        <td class="membership">
                            @if(!$user->status)
                                Archived
                            @elseif($user->hasRole('su'))
                                SU
                            @elseif($user->hasRole('associate'))
                                Associate
                            @elseif($user->hasRole('committee'))
                                Committee
                            @elseif($user->hasRole('super_admin'))
                                Admin
                            @else
                                Member
                            @endif
                        </td>
                        <td class="date">{{ $user->created_at }}</td>
                        <td class="actions">
                            <div class="btn-group">
                                <a class="btn btn-warning btn-sm" href="{{ route('user.edit', $user->username) }}" title="Edit">
                                    <span class="fa fa-edit"></span>
                                </a>
                                <button class="btn btn-danger btn-sm" name="archive-user" title="Archive" value="{{ $user->id }}" {{ !$user->status || $user->id == Auth::user()->id ? ' disabled' : '' }}>
                                    <span class="fa fa-archive"></span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7">Er ... we don't seem to have any user accounts.<br>How are you viewing this?</td>
                </tr>
            @endif
        </tbody>
    </table>
    <div class="form-group">
        {!! Form::select('bulk-action', ['' => '-- Select Action --'] + \App\Http\Controllers\UsersController::$BulkActions, null, ['class' => 'form-control input-sm', 'style' => 'display:inline-block;width:15em;']) !!}
        <button class="btn btn-success btn-sm" name="bulk" style="display:inline-block;margin-top:0.5em;" value="1">
            <span class="fa fa-check"></span>
            <span>Apply to selected users</span>
        </button>
    </div>

    {!! Form::close() !!}
    @include('partials.app.pagination', ['paginator' => $users])
@endsection