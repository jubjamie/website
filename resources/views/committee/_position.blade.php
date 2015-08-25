<div class="col-sm-6 position">
    @if($role && Auth::check() && Auth::user()->can('admin'))
        <a class="btn btn-warning btn-sm edit-button"
           data-toggle="modal"
           data-target="#modal"
           data-modal-template="committee_add"
           data-modal-title="Edit Committee Role"
           data-modal-class="modal-sm"
           data-form-action="{{ route('committee.edit') }}"
           data-mode="edit"
           data-role-id="{{ $role->id }}"
           data-role-name="{{ $role->name }}"
           data-role-email="{{ $role->email }}"
           data-role-desc="{{ $role->description }}"
           data-role-user-id="{{ $role->user_id }}"
           data-role-order="{{ $role->order }}"
           title="Edit this role">
            <span class="fa fa-pencil"></span>
        </a>
    @endif
    <div class="title">{{ $role ? $role->name : '&nbsp;' }}</div>
    <div class="name {{ $role && $role->user ? '' : 'em' }}">{{ $role ? ($role->user ? $role->user->name : '&ndash; unassigned &ndash;'): '&nbsp;' }}</div>
    <div class="picture">
        @if($role)
            <img class="img-rounded" src="{{ $role->user ? $role->user->getAvatarUrl() : '/images/profiles/blank.jpg' }}">
        @endif
    </div>
    <div class="email">
        @if($role)
            <span class="fa fa-envelope"></span>
            <a href="mailto:{{ $role->email }}">{{ $role->email }}</a>
        @endif
    </div>
    <div class="description">
        @if($role)
            {!! nl2br(str_replace('[name]', $role->user ? $role->user->forename : 'They', $role->description)) !!}
        @endif
    </div>
</div>