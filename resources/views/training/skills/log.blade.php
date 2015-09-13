@extends('app')

@section('title', 'Awarded Skills Log')

@section('stylesheets')
    @include('partials.tags.style', ['path' => 'partials/training'])
@endsection

@section('content')
    <h1 class="page-header">@yield('title')</h1>
    <div id="viewSkillsLog">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="skill-date text-center">Date</th>
                    <th class="skill-name">Skill</th>
                    <th class="skill-user">Member</th>
                    <th class="skill-level text-center">Skill Level</th>
                    <th class="skill-user">Awarded By</th>
                </tr>
            </thead>
            <tbody>
                @if(count($skills) > 0)
                    @foreach($skills as $skill)
                        <tr>
                            <td class="skill-date text-center">
                                <div>{{ $skill->updated_at->format('d/m/Y') }}</div>
                                <div>{{ $skill->updated_at->format('H:i:s') }}</div>
                            </td>
                            <td class="skill-name">
                                <div class="name">{{ $skill->skill->name }}</div>
                                <div class="category">{{ $skill->skill->category_id ? $skill->skill->category->name : 'Uncategorised' }}</div>
                            </td>
                            <td class="skill-user">
                                <div class="name">{{ $skill->user->name }}</div>
                                <div class="username">({{ $skill->user->username }})</div>
                            </td>
                            <td class="skill-level text-center">
                                {!! \App\TrainingSkill::getProficiencyHtml($skill->level) !!}
                            </td>
                            <td class="skill-user">
                                <div class="name">{{ $skill->awarder->name }}</div>
                                <div class="username">({{ $skill->awarder->username }})</div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5">No skills have been awarded</td>
                    </tr>
                @endif
            </tbody>
        </table>
        @include('partials.app.pagination', ['paginator' => $skills])
        <p>
            <a class="btn btn-danger" href="{{ route('training.skills.index') }}">
                <span class="fa fa-long-arrow-left"></span>
                <span>Back</span>
            </a>
        </p>
    </div>
@endsection