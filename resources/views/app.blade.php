<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="xsrf-token" content="{{ app('Illuminate\Encryption\Encrypter')->encrypt(csrf_token()) }}">
        <meta name="description" content="Backstage is a student-run society at the University of Bath that provides technical expertise to other Students' Union clubs and societies. We support every kind of event - from small band nights in the Tub to the experiences that are Freshers' Week and Summer Ball.">
        <title>@yield('title') :: Backstage Technical Services</title>
        <link rel="stylesheet" href="/css/app.css">
        @yield('stylesheets')
        <style>
            @yield('styles')
        </style>
    </head>
    <body{{ app()->isDownForMaintenance() || isset($slimFooter) ? ' class=slim-footer' : ''  }}>
        <div id="message-centre">
            <ul>
                @yield('messages')
                @include('partials.flash.flash')
                <noscript>
                    <li>
                        <div class="alert alert-info">
                            <span class="fa fa-exclamation"></span>
                            <span>
                                <h1>Uh oh! No javascript!</h1>
                                <p>We use javascript to improve the user experience and make things more interactive - things may not work if you have
                                    javascript turned off.</p>
                            </span>
                        </div>
                    </li>
                </noscript>
            </ul>
        </div>
        <div id="main-wrapper">
            <div id="header">
                <img src="/images/bts-logo.jpg">
            </div>
            @if(!app()->isDownForMaintenance() && !isset($noNav))
                <div id="nav-wrapper">
                    <nav class="navbar navbar-default wrapper">
                        <div class="container-fluid">
                            <div class="navbar-header">
                                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bts-navbar">
                                    <span class="sr-only">Toggle navigation</span>
                                    <span class="fa fa-bars"></span>
                                </button>
                            </div>
                            <div class="collapse navbar-collapse" id="bts-navbar">
                                {!! $mainNav !!}
                            </div>
                        </div>
                    </nav>
                </div>
            @endif
            <div id="content-wrapper">
                <div id="content">
                    @yield('content')
                </div>
            </div>
        </div>
        <div id="footer">
            <div class="upper">
                @include('partials.app.footer.upper')
            </div>
            <div class="lower">
                @include('partials.app.footer.lower')
            </div>
        </div>
        <div class="modal fade" id="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                </div>
            </div>
        </div>
        <div class="hidden" aria-hidden="true">
            @yield('modal')
        </div>
        <script src="/js/app.js"></script>
        @yield('javascripts')
        @include('tinymce::tpl')
        <script>
            @yield('scripts')
        </script>
    </body>
</html>