<fieldset>
    <legend>Profile Picture</legend>
    <p class="text-center">
        <img class="profile img-rounded" src="{{ $user->getAvatarUrl() }}">
    </p>

    <div class="form-group text-center">
        @if($user->hasAvatar())
            <a class="btn btn-success btn-sm"
               data-toggle="modal"
               data-target="#modal"
               data-modal-template="profile_pic"
               data-modal-class="modal-sm">
                <span class="fa fa-upload"></span>
                <span>Change</span>
            </a>
            <button class="btn btn-danger btn-sm" name="action" value="remove-pic">
                <span class="fa fa-remove"></span>
                <span>Remove</span>
            </button>
        @else
            <a class="btn btn-success btn-sm"
               data-toggle="modal"
               data-target="#modal"
               data-modal-template="profile_pic"
               data-modal-class="modal-sm">
                <span class="fa fa-upload"></span>
                <span>Upload photo</span>
            </a>
        @endif
    </div>
</fieldset>