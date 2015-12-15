@extends('app')

@section('title', 'Edit a Page')

@section('content')
    <h1 class="page-header">Edit a New Page</h1>
    {!! Form::model($page, ['route' => ['page.update', $page->slug], 'style' => 'max-width:700px;']) !!}
    @include('pages.form')

    <div class="form-group">
        <div class="btn-group">
            <button class="btn btn-success" disable-submit="Saving ..." type="submit">
                <span class="fa fa-check"></span>
                <span>Update Page</span>
            </button>
            <a class="btn btn-danger"
               disable-submit="Deleting ..."
               onclick="return confirm('Are you sure you wish to delete this page?\n\nThis process is irreversible.');"
               href="{{ route('page.destroy', $page->slug) }}">
                <span class="fa fa-trash"></span>
                <span>Delete Page</span>
            </a>
            <a class="btn btn-danger" href="{{ route('page.index') }}">
                <span class="fa fa-undo"></span>
                <span>Cancel</span>
            </a>
        </div>
    </div>
    {!! Form::close() !!}
@endsection