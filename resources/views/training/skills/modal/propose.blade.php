<div data-type="modal-template" data-id="propose_skill">
    {!! Form::open() !!}
    <div class="modal-body">
        {{-- Skill --}}
        @include('training.skills.modal._skill_list')

        <div class="form-group">
            {!! Form::label('proposed_level', 'What level do you feel you should be awarded?', ['class' => 'control-label']) !!}
            <select class="form-control" name="proposed_level" id="proposed_level">
                @for($i = 1; $i <= 3; $i++)
                    @if(isset($skill))
                    <option value="{{ $i }}"{{ $awardedSkill && $i <= $awardedSkill->level ? ' disabled' : '' }}{{ $awardedSkill && ($awardedSkill->level + 1) == $i }}>{{ \App\TrainingSkill::$LevelNames[$i] }}</option>
                    @else
                        <option value="{{ $i }}">{{ \App\TrainingSkill::$LevelNames[$i] }}</option>
                    @endif
                @endfor
            </select>
        </div>
        <div class="form-group">
            {!! Form::label('reasoning', 'Please provide some reasoning:', ['class' => 'control-label']) !!}
            {!! Form::textarea('reasoning', null, ['class' => 'form-control', 'placeholder' => 'Please provide some specific examples - the more specific you are the greater the chance of being awarded this skill', 'rows' => 5]) !!}
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-success" data-form-action="{{ route('training.skills.propose') }}" data-type="submit-modal" type="button">
            <span class="fa fa-check"></span>
            <span>Submit proposal</span>
        </button>
    </div>
    {!! Form::close() !!}
</div>