<div class="tab-pane" id="{{ $category->id ? "category_{$category->id}" : "uncategorised" }}">
    <table class="table table-striped">
        <tbody>
            @if(count($category->skills) > 0)
                @foreach($category->skills as $skill)
                    <tr>
                        <td class="skill-name">
                            <a class="grey" href="{{ route('training.skills.view', $skill->id) }}">{{ $skill->name }}</a>
                        </td>
                        <td class="skill-buttons">
                            @if(Auth::check() && Auth::user()->isAdmin())
                                <div class="btn-group">
                                    <button class="btn btn-danger btn-sm"
                                            data-submit-ajax="{{ route('training.skills.delete', $skill->id) }}"
                                            data-submit-confirm="Are you sure you want to delete this skill?"
                                            data-success-url="{{ Request::url() }}"
                                            type="button">
                                        <span class="fa fa-remove"></span>
                                    </button>
                                </div>
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