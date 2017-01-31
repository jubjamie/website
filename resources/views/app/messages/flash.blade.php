@if(Session::has('flash.alerts'))
    @foreach(Session::pull('flash.alerts') as $alert)
        @include('app.messages.message', [
            'level' => $alert['level'],
            'title' => $alert['title'],
            'message' => $alert['message']
        ])
    @endforeach
@endif