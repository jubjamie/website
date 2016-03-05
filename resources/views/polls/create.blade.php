@extends('app')
@section('page-section', 'polls')
@section('title', 'Create Poll')

@section('content')
    <h1 class="page-header">Create Poll</h1>
    {!! Form::open(['route' => 'polls.store', 'id' => 'createPoll']) !!}
        <h2>Question</h2>
        <div class="form-group @include('partials.form.error-class', ['name' => 'question'])">
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-question"></span></span>
                {!! Form::text('question', null, ['class' => 'form-control', 'placeholder' => 'Enter the question']) !!}
            </div>
            @include('partials.form.input-error', ['name' => 'question'])
        </div>
        <div class="form-group @include('partials.form.error-class', ['name' => 'description'])">
            <div class="input-group textarea">
                <span class="input-group-addon"><span class="fa fa-bars"></span></span>
                {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'You can enter a more detailed description to elaborate on or explain your question', 'rows' => 4]) !!}
            </div>
            @include('partials.form.input-error', ['name' => 'description'])
        </div>

        <h2>Answers</h2>
        @foreach($options as $i => $value)
            <div class="form-group @include('partials.form.error-class', ['name' => 'option.' . $i])">
                <div class="input-group">
                    <span class="input-group-addon">{{ $i }}.</span>
                    {!! Form::text('option[' . $i . ']', null, ['class' => 'form-control', 'placeholder' => 'Answer ' . $i]) !!}
					<span class="input-group-addon">
						<button class="btn btn-danger btn-xs" formaction="{{ route('polls.store.delOption') }}" name="deleteOption" title="Remove this answer" value="{{ $i }}">
                            <span class="fa fa-times"></span>
                        </button>
					</span>
                </div>
                @include('partials.form.input-error', ['name' => 'option.' . $i])
            </div>
        @endforeach
        <div class="form-group">
            <button class="btn btn-success" formaction="{{ route('polls.store.addOption') }}" id="addPollOption" name="addOption">
                <span class="fa fa-plus"></span>
                <span>Add another answer</span>
            </button>
        </div>
        <div class="form-group">
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('show_results', '1') !!}
                    Allow members to see the results before voting.
                </label>
            </div>
        </div>
        <div class="form-group">
            <button class="btn btn-success" disable-submit="Creating poll ..." name="createPoll">
                <span class="fa fa-check"></span>
                <span>Create Poll</span>
            </button>
            <a class="btn btn-danger" href="{{ route('polls.index') }}">
                <span class="fa fa-undo"></span>
                <span>Cancel</span>
            </a>
        </div>
    {!! Form::close() !!}
@endsection