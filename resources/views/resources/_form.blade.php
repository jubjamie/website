@extends('app')

@if($mode == 'create')
@section('scripts')
    function toggleResourceType() {
    var wrapper = $('#resourceTypeToggle');
    var newType = $('form#createResource').find('select[name=type]').val();
    wrapper.children('div[data-toggle-type]').hide();
    wrapper.children('div[data-toggle-type=' + newType + ']').show();
    }
    toggleResourceType();
    $('form#createResource').find('select[name=type]').on('change', toggleResourceType);
@endsection
@endif

@section('content')
    <h1 class="page-header">Resources</h1>
    <h2 class="page-header">@yield('title')</h2>

    {!! Form::model($resource, ['url' => $url, 'class' => 'form-horizontal', 'enctype' => 'multipart/form-data']) !!}
    {{-- Name --}}
    <div class="form-group @InputClass('title')">
        {!! Form::label('title', 'Title:', ['class' => 'control-label col-md-4']) !!}
        <div class="col-md-8">
            {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'What is it called?']) !!}
            @InputError('title')
        </div>
    </div>

    {{-- Description --}}
    <div class="form-group @InputClass('description')">
        {!! Form::label('description', 'Description:', ['class' => 'control-label col-md-4']) !!}
        <div class="col-md-8">
            {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'This is optional but will help people when searching']) !!}
            @InputError('description')
        </div>
    </div>

    {{-- Type --}}
    @if($mode =='create')
        <div class="form-group @InputClass('type')">
            {!! Form::label('type', 'Type:', ['class' => 'control-label col-md-4']) !!}
            <div class="col-md-8">
                {!! Form::radioGroup('type', \App\Resource::TYPES, null, ['data-type' => 'toggle-visibility']) !!}
                @InputError('type')
            </div>
        </div>
    @elseif($mode == 'edit')
        {!! Form::input('hidden', 'type') !!}
    @endif

    {{-- Source --}}
    <div class="form-group{{ $resource->type == \App\Resource::TYPE_FILE ? '' : ' hidden' }} @InputClass('file')"
         data-visibility-id="{{ \App\Resource::TYPE_FILE }}">
        {!! Form::label('file', 'Select File:', ['class' => 'control-label col-md-4']) !!}
        <div class="col-md-8">
            {!! Form::file('file') !!}
            @InputError('file')
            @if($mode == 'edit')
                <p class="help-block alt">Leave blank to keep the current file</p>
            @endif
        </div>
    </div>
    <div class="form-group{{ $resource->type == \App\Resource::TYPE_GDOC ? '' : ' hidden' }} @InputClass('drive_id')"
         data-visibility-id="{{ \App\Resource::TYPE_GDOC }}">
        {!! Form::label('drive_id', 'Document ID:', ['class' => 'control-label col-md-4']) !!}
        <div class="col-md-8">
            {!! Form::text('drive_id', null, ['class' => 'form-control', 'placeholder' => 'Enter the ID of the Google Drive document']) !!}
            @InputError('drive_id')
        </div>
    </div>

    {{-- Category --}}
    <div class="form-group @InputClass('category')">
        {!! Form::label('category_id', 'Category:', ['class' => 'control-label col-md-4']) !!}
        <div class="col-md-8">
            {!! Form::select('category_id', ['' => '-- Uncategorised --'] + $categories->all(), null, ['class' => 'form-control']) !!}
            @InputError('category')
        </div>
    </div>

    {{-- Tags --}}
    <div class="form-group @InputClass('tags')">
        {!! Form::label('tags[]', 'Tags:', ['class' => 'control-label col-md-4']) !!}
        <div class="col-md-8">
            {!! Form::select('tags[]', $tags->all(), null, ['class' => 'form-control', 'multiple' => 'multiple', 'select2' => 'Use this to group it with similar documents']) !!}
            @InputError('tags')
        </div>
    </div>

    {{-- Attach to an event --}}
    <div class="form-group @InputClass('event_id')">
        {!! Form::label('event_id', 'Link to Event:', ['class' => 'control-label col-md-4']) !!}
        <div class="col-md-8">
            {!! Form::text('event_id', null, ['class' => 'form-control form-control-inline', 'placeholder' => 'Enter it\'s ID', 'width' => '5']) !!}
            @InputError('event_id')
        </div>
    </div>

    {{-- Access --}}
    <div class="form-group @InputClass('access')">
        {!! Form::label('access_id', 'Access:', ['class' => 'control-label col-md-4']) !!}
        <div class="col-md-8">
            {!! Form::select('access_id', $access, $resource->id ? null : \App\Permission::whereName('resources.registered')->first()->id, ['class' => 'form-control']) !!}
            @InputError('access')
            <p class="help-block alt">Select who should be able to view this resource</p>
        </div>
    </div>

    {{-- Buttons --}}
    <div class="form-group">
        <div class="col-md-4"></div>
        <div class="col-md-8">
            <div class="btn-group">
                @if($mode =='create')
                    <button class="btn btn-success" disable-submit="Creating">
                        <span class="fa fa-check"></span>
                        <span>Create</span>
                    </button>
                    <a class="btn btn-danger" href="{{ route('resources.index') }}" onclick="history.back()">
                        <span class="fa fa-long-arrow-left"></span>
                        <span>Cancel</span>
                    </a>
                @elseif($mode == 'edit')
                    <button class="btn btn-success" disable-submit="Saving">
                        <span class="fa fa-check"></span>
                        <span>Save</span>
                    </button>
                    <a class="btn btn-danger" href="{{ route('resources.view', ['id' => $resource->id]) }}" onclick="history.back()">
                        <span class="fa fa-long-arrow-left"></span>
                        <span>Cancel</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection