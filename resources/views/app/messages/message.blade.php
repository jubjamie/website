<?php
    if($level == 'danger') {
        $perm = true;
    }
?>
<li>
    <div class="alert alert-{{ $level }}{{ isset($perm) && $perm ? ' alert-perm' : '' }}"{{ isset($id) ? ' id='.$id : '' }}>
        @if(isset($MessageIcons[$level]))
            <span class="fa fa-{{ $MessageIcons[$level] }}"></span>
        @else
            <span class="fa fa-exclamation"></span>
        @endif
        <span>
            @if(!empty($title))
                <h1>{{ $title }}</h1>
            @endif
            <p>{!! str_replace(PHP_EOL, '</p><p>', $message) !!}</p>
        </span>
    </div>
</li>