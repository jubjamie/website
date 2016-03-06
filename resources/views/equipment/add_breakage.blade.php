@extends('app')
@section('page-section', 'equipment')
@section('page-id', 'add_breakage')
@section('title', 'Report a Breakage')

@section('scripts')
    $('#addBreakage').tabify();
@endsection

@section('content')
    <h1 class="page-header">@yield('title')</h1>
    <div class="tabpanel" id="addBreakage">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#">Backstage</a></li>
            <li><a href="#">Bath SU</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active">
                @include('equipment.create_breakage.bts')
            </div>
            <div class="tab-pane">
                @include('equipment.create_breakage.emp')
            </div>
        </div>
    </div>
@endsection