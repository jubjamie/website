@extends('app')
@section('page-section', 'elections')
@section('page-id', 'list')
@section('title', 'Elections')

@section('content')
    <h1 class="page-header">@yield('title')</h1>

    <div class="container-inner">
        @if($activeUser->isAdmin())
            <p>
                <a class="btn btn-success" href="{{ route('elections.create') }}">
                    <span class="fa fa-plus"></span>
                    <span>Create Election</span>
                </a>
            </p>
        @endif
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="title">Election</th>
                    <th class="positions">Positions</th>
                    <th class="admin-tools"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($elections as $election)
                    <tr>
                        <td class="title">
                            {!! link_to_route('elections.view', $election->title, ['id' => $election->id]) !!}
                        </td>
                        <td class="positions">
                            @if($election->isFull())
                                <em>(entire committee)</em>
                            @else
                                <ul class="position-list">
                                    @foreach($election->positions as $position)
                                        <li>{{ $position }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>
                        <td class="admin-tools admin-tools-icon">
                            @if($activeUser->isAdmin())
                                <div class="dropdown admin-tools">
                                    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                        <span class="fa fa-cog"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li>
                                            <a href="{{ route('elections.edit', ['id' => $election->id]) }}">
                                                <span class="fa fa-pencil"></span> Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a data-submit-ajax="{{ route('elections.delete', ['id' => $election->id]) }}" data-submit-confirm="Are you sure you want to delete this election?">
                                                <span class="fa fa-trash"></span> Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No elections</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('partials.app.pagination', ['paginator' => $elections])
@endsection