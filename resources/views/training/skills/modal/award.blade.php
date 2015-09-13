<div data-type="modal-template" data-id="award_skill">
    {!! Form::open() !!}
    <div class="modal-body">
        <p style="margin-top:0;">If you are an admin, or are <strong>{{ \App\TrainingSkill::$LevelNames[3] }}</strong> in {{ isset($skill) ? 'this' : 'a' }} skill, you can award it to any other member.</p>

        {{-- Skill --}}
        @include('training.skills.modal._skill_list')

        {{-- Member --}}
        <div class="form-group">
            {!! Form::label('user_id', 'Select the member to award the skill to:', ['class' => 'control-label']) !!}
            {!! Form::select('user_id', $members, null, ['class' => 'form-control']) !!}
        </div>

        {{-- Level --}}
        <div class="form-group">
            {!! Form::label('level', 'What level should they be awarded?', ['class' => 'control-label']) !!}
            {!! Form::select('level', [1 => \App\TrainingSkill::$LevelNames[1], 2 => \App\TrainingSkill::$LevelNames[2], 3 => \App\TrainingSkill::$LevelNames[3]], null, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-success" data-type="submit-modal" data-form-action="{{ route('training.skills.award') }}" type="button">
            <span class="fa fa-check"></span>
            <span>Award skill</span>
        </button>
    </div>
    {!! Form::close() !!}
</div>