<table class="table table-striped">
    <thead>
        <th class="proposal-status"></th>
        <th class="proposal-skill">Skill</th>
        <th class="proposal-user">User</th>
        <th class="proposal-level">Proposed</th>
        <th class="awarded-level">{{ $isListOfReviewed ? 'Awarded' : '' }}</th>
        <th class="awarded-user">{{ $isListOfReviewed ? 'Awarded by' : '' }}</th>
    </thead>
    <tbody>
        @if(count($proposals) > 0)
            @foreach($proposals as $proposal)
                <tr onclick="window.location='{{ route('training.skills.proposal.view', $proposal->id) }}';">
                    <td class="proposal-status">
                        @if($proposal->isAwarded())
                            @if($proposal->awarded_level == $proposal->proposed_level)
                                <span class="fa fa-check success" title="Awarded"></span>
                            @elseif($proposal->awarded_level > 0)
                                <span class="fa fa-exclamation warning" title="Awarded a lower skill level"></span>
                            @else
                                <span class="fa fa-remove danger" title="Not awarded"></span>
                            @endif
                        @endif
                    </td>
                    <td class="dual-layer">
                        <div class="upper">{{ $proposal->skill->name }}</div>
                        <div class="lower">{{ $proposal->skill->category_id ? $proposal->skill->category->name : "Uncategorised" }}</div>
                    </td>
                    <td class="dual-layer">
                        <div class="upper">{{ $proposal->user->name }}</div>
                        <div class="lower">{{ $proposal->user->username }}</div>
                    </td>
                    <td class="skill-level">
                        {!! \App\TrainingSkill::getProficiencyHtml($proposal->proposed_level) !!}
                    </td>
                    <td class="skill-level">
                        @if($proposal->isAwarded())
                            {!! $proposal->awarded_level === 0 ? '<em>none</em>' : \App\TrainingSkill::getProficiencyHtml($proposal->awarded_level) !!}
                        @endif
                    </td>
                    <td class="dual-layer">
                        @if($proposal->isAwarded())
                            <div class="upper">{{ $proposal->awarder->name }}</div>
                            <div class="lower">{{ $proposal->awarder->username }}</div>
                        @endif
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="6">{{ $isListOfReviewed ? 'There are no reviewed skill proposals' : 'There are no skill proposals requiring review' }}</td>
            </tr>
        @endif
    </tbody>
</table>