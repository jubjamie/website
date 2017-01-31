<noscript>
    @include('app.messages.message', [
        'level' => 'warning',
        'title' => 'Uh oh! No javascript!',
        'message' => 'We use javascript to improve the user experience and make things more interactive - things may not work if you have javascript turned off.',
        'perm' => true
    ])
</noscript>
@if(!Session::has('CookiePolicyAccepted'))
    @include('app.messages.message', [
        'level' => 'info',
        'title' => 'Cookie policy',
        'message' => 'Some rubbish about our cookie policy with a <a href="#">link to the policy</a>.',
        'perm' => true,
        'id' => 'cookie-policy-msg'
    ])
@endif