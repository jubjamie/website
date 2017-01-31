<li>
    <div class="alert alert-{{ $level }}{{ isset($perm) && $perm ? ' alert-perm' : '' }}"{{ isset($id) ? ' id='.$id : '' }}>
        @if(isset($flashIcons[$level]))
            <span class="fa fa-{{ $flashIcons[$level] }}"></span>
        @else
            <span class="fa fa-exclamation"></span>
        @endif
        <span>
            @if(!empty($title))
                <h1>{{ $title }}</h1>
            @endif
            <p>{!! str_replace(PHP_EOL, '</p><p>', $message) !!}</p>
        </span>
        @if($perm)
            <button class="close">
                <span class="fa fa-remove"></span>
            </button>
        @endif
    </div>
</li>