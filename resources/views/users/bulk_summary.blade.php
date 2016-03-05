@extends('app')
@section('page-section', 'users')
@section('title', 'Create User Summary')

@section('content')
    <h1 class="page-header">@yield('title')</h1>
    <div id="bulkSummary">
        <div class="container-fluid">
            @foreach($results as $i => $result)
                <div class="row {{ $result['success'] ? 'success' : 'error' }}">
                    <div class="status"><span class="fa {{ $result['success'] ? 'fa-check' : 'fa-remove' }}"></span></div>
                    <div class="username">{{ is_int($result['username']) ? 'User ' : '' }}{{ $result['username'] }}</div>
                    <div class="details">{!! nl2br($result['message']) !!}</div>
                </div>
            @endforeach
        </div>
        <div class="form-group">
            <a class="btn btn-success" href="{{ route('user.create') }}">
                <span class="fa fa-user-plus"></span>
                <span>Add more users</span>
            </a>
            <a class="btn btn-danger" href="{{ route('user.index') }}">
                <span class="fa fa-long-arrow-left"></span>
                <span>Go back to the list</span>
            </a>
        </div>
    </div>
@endsection