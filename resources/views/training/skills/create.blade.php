@extends('app')

@section('title', 'Add Skill')

@section('stylesheets')
    @include('partials.tags.style', ['path' => 'partials/training'])
@endsection

@section('content')
    <h1 class="page-header">Add Training Skill</h1>
    <div id="createSkill">
        {!! Form::open(['route' => ['training.skills.add.do']]) !!}
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <fieldset>
                        <legend>General Details</legend>
                        {{-- Name --}}
                        <div class="form-group @include('partials.form.error-class', ['name' => 'name'])">
                            {!! Form::label('name', 'Name:', ['class' => 'control-label']) !!}
                            {!! Form::text('name', null, ['class' => 'form-control']) !!}
                            @include('partials.form.input-error', ['name' => 'name'])
                        </div>

                        {{-- Category --}}
                        <div class="form-group @include('partials.form.error-class', ['name' => 'category_id'])">
                            {!! Form::label('category_id', 'Category:', ['class' => 'control-label']) !!}
                            {!! Form::select('category_id', \App\TrainingCategory::selectList(), null, ['class' => 'form-control']) !!}
                            @include('partials.form.input-error', ['name' => 'category_id'])
                        </div>

                        {{-- Description --}}
                        <div class="form-group @include('partials.form.error-class', ['name' => 'description'])">
                            {!! Form::label('description', 'Description:', ['class' => 'control-label']) !!}
                            {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Please detail what this skill is for and, if applicable, what it allows you to do', 'rows' => 8]) !!}
                            @include('partials.form.input-error', ['name' => 'description'])
                        </div>

                        {{-- Buttons --}}
                        <div class="form-group">
                            <div class="btn-group">
                                <button class="btn btn-success" disable-submit="Adding skill ...">
                                    <span class="fa fa-plus"></span>
                                    <span>Add skill</span>
                                </button>
                                <a class="btn btn-danger" href="{{ route('training.skills.index') }}">
                                    <span class="fa fa-undo"></span>
                                    <span>Cancel</span>
                                </a>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="col-md-6">
                    <fieldset>
                        <legend>Level Requirements</legend>

                        {{-- Level 1 --}}
                        <div class="form-group @include('partials.form.error-class', ['name' => 'requirements_level1'])">
                            {!! Form::label('requirements_level1', \App\TrainingSkill::$LevelNames[1] . ':', ['class' => 'control-label']) !!}
                            {!! Form::textarea('requirements_level1', null, ['class' => 'form-control', 'placeholder' => 'This is generally for the ability to perform the task while supervised by a member who is ' . \App\TrainingSkill::$LevelNames[3], 'rows' => 4]) !!}
                            @include('partials.form.input-error', ['name' => 'requirements_level1'])
                        </div>

                        {{-- Level 2 --}}
                        <div class="form-group @include('partials.form.error-class', ['name' => 'requirements_level2'])">
                            {!! Form::label('requirements_level2', \App\TrainingSkill::$LevelNames[2] . ':', ['class' => 'control-label']) !!}
                            {!! Form::textarea('requirements_level2', 'Have independently completed the requirements without significant help from another member', ['class' => 'form-control', 'placeholder' => 'This is generally for the ability to perform the task while unsupervised', 'rows' => 4]) !!}
                            @include('partials.form.input-error', ['name' => 'requirements_level2'])
                        </div>

                        {{-- Level 3 --}}
                        <div class="form-group @include('partials.form.error-class', ['name' => 'requirements_level3'])">
                            {!! Form::label('requirements_level3', \App\TrainingSkill::$LevelNames[3]. ':', ['class' => 'control-label']) !!}
                            {!! Form::textarea('requirements_level3', 'Have reached a level where knowledge is sufficient to be able to train other members in this skill.', ['class' => 'form-control', 'placeholder' => 'This is generally for the ability to teach, supervise and approve other members', 'rows' => 4]) !!}
                            @include('partials.form.input-error', ['name' => 'requirements_level3'])
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
@endsection