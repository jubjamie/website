@extends('app')

@section('title', $skill->name . ' :: Training Skill')

@section('stylesheets')
    @include('partials.tags.style', ['path' => 'partials/training'])
@endsection

@section('content')
    <h1 class="page-header">Training Skill</h1>
    <div id="viewSkill">
        @if($activeUser->isAdmin())
            <h2>
                <span data-editable="true"
                      data-edit-type="text"
                      data-edit-url="{{ route('training.skills.update', $skill->id) }}"
                      data-control-name="name"
                      role="button">{{ $skill->name }}</span>
            </h2>
            <h4 class="category">
                <span data-editable="true"
                      data-edit-type="select"
                      data-edit-source="categories"
                      data-edit-url="{{ route('training.skills.update', $skill->id) }}"
                      data-control-name="category_id"
                      data-text-format="category"
                      data-value="{{ $skill->category_id ?: null }}">[{{ $skill->category ? $skill->category->name : 'Uncategorised' }}]</span>
            </h4>
            <div class="description">
                <div data-editable="true"
                      data-edit-type="textarea"
                      data-edit-url="{{ route('training.skills.update', ['id' => $skill->id]) }}"
                      data-control-name="description"
                      role="button">{!! nl2br($skill->description) !!}</div>
            </div>
        @else
            <h2>{{ $skill->name }}</h2>
            <h4 class="category">[{{ $skill->category ? $skill->category->name : 'Uncategorised' }}]</h4>
            <div class="description">{!! nl2br($skill->description) !!}</div>
        @endif
        <h3>Level Requirements</h3>
        <table class="table table-striped level-requirements">
            @for($i = 1; $i <= 3; $i++)
                <tr>
                    <td>{!! \App\TrainingSkill::getProficiencyHtml($i) !!}<br><span class="small">{{ \App\TrainingSkill::$LevelNames[$i] }}</span></td>
                    <td>
                        @if($activeUser->isAdmin())
                            <div data-editable="true"
                                  data-edit-type="textarea"
                                  data-edit-url="{{ route('training.skills.update', ['id' => $skill->id]) }}"
                                  data-control-name="requirements_level{{ $i }}"
                                  role="button">{!! nl2br($skill->{'requirements_level'.$i}) !!}</div>
                        @else
                            {!! nl2br($skill->{'requirements_level'.$i}) !!}
                        @endif
                    </td>
                </tr>
            @endfor
        </table>
        <h3>Members With This Skill</h3>
        <div class="table table-striped member-list">
            <div class="thead">
                <div class="row">
                    <div class="th">{!! \App\TrainingSkill::getProficiencyHtml(3) !!}</div>
                    <div class="th">{!! \App\TrainingSkill::getProficiencyHtml(2) !!}</div>
                    <div class="th">{!! \App\TrainingSkill::getProficiencyHtml(1) !!}</div>
                </div>
            </div>
            <div class="tbody">
                @for($i = 0; $i < max(count(max($awarded_users)), 1); $i++)
                    <div class="row">
                        @for($j = 3; $j >= 1; $j--)
                            <div>
                                @if(isset($awarded_users[$j][$i]))
                                    <a href="{{ route('members.profile', $awarded_users[$j][$i]->username) }}#training">{{ $awarded_users[$j][$i]->name }}</a>
                                    @if(Auth::check() && Auth::user()->id == $awarded_users[$j][$i]->id)
                                        (me)
                                    @endif
                                @elseif($i == 0)
                                    <div class="text-center em">no members</div>
                                @endif
                            </div>
                        @endfor
                    </div>
                @endfor
            </div>
        </div>
        <p style="margin-top:3em;">
            <div class="btn-group">
                @if($activeUser->isMember() && (!$awardedSkill || $awardedSkill->level < 3))
                    <a class="btn btn-success"
                       data-target="#modal"
                       data-toggle="modal"
                       data-modal-class="modal-sm"
                       data-modal-template="propose_skill"
                       data-modal-title="Propose Skill Level"
                       href="#">
                        <span class="fa fa-plus"></span>
                        <span>Propose skill level</span>
                    </a>
                @endif
                @if($activeUser->isAdmin() || ($awardedSkill && $awardedSkill->level == 3))
                    <a class="btn btn-success"
                       data-target="#modal"
                       data-toggle="modal"
                       data-modal-class="modal-sm"
                       data-modal-template="award_skill"
                       data-modal-title="Award Skill Level"
                       href="#">
                        <span class="fa fa-user-plus"></span>
                        <span>Award skill</span>
                    </a>
                    <a class="btn btn-danger"
                       data-toggle="modal"
                       data-target="#modal"
                       data-modal-class="modal-sm"
                       data-modal-template="revoke_skill">
                        <span class="fa fa-user-times"></span>
                        <span>Revoke skill</span>
                    </a>
                @endif
            </div>
        </p>
        <p>
            <div class="btn-group">
                <a class="btn btn-danger" href="{{ route('training.skills.index') }}">
                    <span class="fa fa-long-arrow-left"></span>
                    <span>Back</span>
                </a>
                @if($activeUser->isAdmin())
                    <button class="btn btn-danger"
                            data-submit-ajax="{{ route('training.skills.delete', $skill->id) }}"
                            data-submit-confirm="Are you sure you want to delete this skill?"
                            data-success-url="{{ route('training.skills.index') }}"
                            type="button">
                        <span class="fa fa-remove"></span>
                        <span>Delete this skill</span>
                    </button>
                @endif
            </div>
        </p>
    </div>
@endsection

@section('modal')
    @if($activeUser->isMember() && (!$awardedSkill || $awardedSkill->level < 3))
        @include('training.skills.modal.propose', ['skill' => $skill])
    @endif
    @if($activeUser->isAdmin() || ($awardedSkill && $awardedSkill->level == 3))
        @include('training.skills.modal.award', ['skill' => $skill])
        @include('training.skills.modal.revoke', ['skill' => $skill])
    @endif
    @if($activeUser->isAdmin())
        <div data-type="data-select-source" data-select-name="categories">
            {!! Form::select('category_id', \App\TrainingCategory::selectList(), null, ['class' => 'form-control']) !!}
        </div>
        <div data-type="data-text-format" data-name="category" data-config="{{ json_encode(['text' => [null => 'Uncategorised']]) }}">
            [#text]
        </div>
    @endif
@endsection