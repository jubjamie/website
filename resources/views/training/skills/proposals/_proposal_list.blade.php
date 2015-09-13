<table class="table table-striped">
    <thead>
        <th class="proposal-status"></th>
        <th class="proposal-skill">Skill</th>
        <th class="proposal-user">User</th>
        <th class="proposal-level">Proposed</th>
        <th class="awarded-level">Awarded</th>
        <th class="awarded-user">Awarded by</th>
    </thead>
    <tbody>
        @if(count($proposals) > 0)
            @foreach($proposals as $proposal)
                <tr onclick="window.location='{{ route('training.skills.proposal.view', $proposal->id) }}';">
                    <td class="proposal-status">
                                            <span class="fa fa-{{ $proposal->isAwarded() ? 'check success' : 'remove danger' }}"
                                                  title="{{ $proposal->isAwarded() ? 'Reviewed' : 'Not Reviewed' }}"></span>
                    </td>
                    <td class="proposal-skill">{{ $proposal->skill->name }}</td>
                    <td class="proposal-user">
                        <div class="name">{{ $proposal->user->name }}</div>
                        <div class="username">({{ $proposal->user->username }})</div>
                    </td>
                    <td class="skill-level">
                        {!! \App\TrainingSkill::getProficiencyHtml($proposal->proposed_level) !!}
                    </td>
                    <td class="skill-level">
                        @if($proposal->isAwarded())
                            {!! $proposal->awarded_level === 0 ? '<em>none</em>' : \App\TrainingSkill::getProficiencyHtml($proposal->awarded_level) !!}
                        @else
                            <em>n/a</em>
                        @endif
                    </td>
                    <td class="awarded-user">
                        @if($proposal->isAwarded())
                            <div class="name">{{ $proposal->awarder->name }}</div>
                            <div class="username">({{ $proposal->awarder->username }})</div>
                        @else
                            <em>n/a</em>
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