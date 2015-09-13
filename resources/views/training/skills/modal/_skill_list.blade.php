<div class="form-group">
    @if(isset($skill))
        {!! Form::input('hidden', 'skill_id', $skill->id) !!}
    @else
        {!! Form::label('skill_id', 'Skill:', ['class' => 'control-label']) !!}
        {!! Form::select('skill_id', $skillList, null, ['class' => 'form-control']) !!}
    @endif
</div>