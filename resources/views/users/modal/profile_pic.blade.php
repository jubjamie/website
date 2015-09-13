<div data-type="modal-template" data-id="profile_pic">
    {!! Form::open(['files' => true]) !!}
    <div class="modal-header">
        <h1>Change profile picture</h1>
    </div>
    <div class="modal-body">
        <div class="form-group">
            {!! Form::label('avatar', 'Select ' . (@$ownProfile ? 'your' : 'their') . ' new picture') !!}
            {!! Form::file('avatar') !!}
            <p class="em" style="font-size:12px;">This automatically resizes the image to 500x500px, while maintaining its aspect ratio. At the moment you don't have any control over this - I will look into improving this in the future.</p>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-success" disable-submit="Setting picture ..." name="action" value="change-pic">
            <span class="fa fa-check"></span>
            <span>Set profile picture</span>
        </button>
    </div>
    {!! Form::close() !!}
</div>