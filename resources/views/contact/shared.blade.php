@extends('app.main')

@section('page-section', 'contact')
@section('header-main', 'Contact Us')

@section('content')
    <div class="tabpanel">
        {!! $menu !!}
        <div class="tab-content">
            <div class="tab-pane active">
                @yield('tab')
            </div>
            <div class="tab-pane"></div>
            <div class="tab-pane"></div>
        </div>
    </div>
@endsection