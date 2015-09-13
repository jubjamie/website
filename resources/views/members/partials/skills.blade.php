<div class="container-fluid" id="listSkills">
    <div class="row">
        <div class="col-md-4">
            <nav>
                <ul class="nav nav-pills nav-stacked category-list" role="tablist">
                    @foreach($skillCategories as $i => $category)
                        <li {{ $i == 0 ? ' class=active' : '' }}>
                            <a data-toggle="tab" href="#{{ $category->id ? "category_{$category->id}" : "uncategorised" }}" role="button">{{ $category->name }}</a>
                            <span class="label label-default">{{ $user->countSkills($category->id ?: -1) }} / {{ count($category->skills) }}</span>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
        <div class="col-md-8">
            <div class="tab-content">
                @foreach($skillCategories as $i => $category)
                    <div class="tab-pane{{ $i == 0 ? ' active' : '' }}" id="{{ $category->id ? "category_{$category->id}" : "uncategorised" }}">
                        <table class="table table-striped user-skills">
                            <tbody>
                                @if(count($category->skills) > 0)
                                    @foreach($category->skills as $skill)
                                        <tr class="{{ $user->hasSkill($skill) ? 'has-skill' : '' }}">
                                            <td class="skill-name">
                                                <a class="grey" href="{{ route('training.skills.view', $skill->id) }}">{{ $skill->name }}</a>
                                            </td>
                                            <td class="skill-level">
                                                @if($user->hasSkill($skill))
                                                    {!! \App\TrainingSkill::getProficiencyHtml($user->getSkill($skill)->level) !!}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="2"><em>&ndash; there aren't any skills in this category &ndash;</em></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>