@extends('app.main')

@section('title', $page->title)
@section('page-section', 'pages')
@section('page-id', 'view')
@section('header-main', $page->title)

@section('content')
    {!! $page->content !!}
@endsection