@extends('app')
@section('page-section', 'equipment')
@section('title', 'Breakages and Repairs')

@section('add_breakage_link')
    <p>
        <a class="btn btn-success" href="{{ route('equipment.repairs.add') }}">
            <span class="fa fa-wrench"></span>
            <span>Report a breakage</span>
        </a>
    </p>
@endsection

@section('content')
    <h1 class="page-header">@yield('title')</h1>
    <div id="repairsDb">
        @yield('add_breakage_link')
        <table class="table table-striped">
            <thead>
                <th class="item">Item</th>
                <th class="description">Description</th>
                <th class="comment hidden-xs hidden-sm">Comments</th>
                <th class="date">Reported</th>
                <th class="status">Status</th>
            </thead>
            <tbody>
                @if(count($breakages) > 0)
                    @foreach($breakages as $breakage)
                        <tr onclick="document.location='{{ route('equipment.repairs.view', $breakage->id) }}';">
                            <td class="item">
                                <p class="name">{{ $breakage->name }}</p>

                                <p class="location">{{ $breakage->location }}</p>
                            </td>
                            <td class="description">{!! nl2br($breakage->description) !!}</td>
                            <td class="comment hidden-xs hidden-sm">{!! nl2br($breakage->comment) !!}</td>
                            <td class="date">{{ $breakage->created_at->diffForHumans() }}</td>
                            <td class="status">{{ App\EquipmentBreakage::$status[$breakage->status] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5">We seem to be breakage-free at the moment.<br>Let's keep it up!</td>
                    </tr>
                @endif
            </tbody>
        </table>
        @include('partials.app.pagination', ['paginator' => $breakages])
        @yield('add_breakage_link')
    </div>
@endsection