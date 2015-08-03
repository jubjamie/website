<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title') :: Backstage Technical Services</title>
        <link rel="stylesheet" href="/css/app.css">
        @yield('stylesheets')
        <script src="/js/app.js"></script>
        <style>
            @yield('styles')
        </style>
    </head>
    <body>
        <div id="message-centre">
            <ul>
            @include('partials.flash.flash')
            <noscript>
                <li>
                <div class="alert alert-info">
                    <span class="fa fa-exclamation"></span>
						<span>
							<h1>Uh oh! No javascript!</h1>
							<p>We use javascript to improve the user experience and make things more interactive - things may not work if you have javascript turned off.</p>
						</span>
                </div>
                </li>
            </noscript>
            </ul>
        </div>
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
        <div id="footer-wrapper">
            <div class="container-fluid" id="footer">
                <div class="col-sm-4">
                    @include('partials.app.footer.left')
                </div>
                <div class="col-sm-4 text-center">
                    @include('partials.app.footer.centre')
                </div>
                <div class="col-sm-4 text-right">
                    @include('partials.app.footer.right')
                </div>
            </div>
        </div>
        @yield('modal')
        @yield('javascripts')
        @include('tinymce::tpl')
        <script>
            function clearModalForm($form) {
                $form.find('.has-error,.has-success');
                $form.find('.has-error').removeClass('has-error').children('p.errormsg').remove();
                $form.find('.has-success').removeClass('has-success');
            }
            function processFormErrors(form, errors)
            {
                if(typeof(errors) == "object") {
                    form.find('input,textarea,select').each(function () {
                        var $input = $(this);
                        var $group = $input.parents('.form-group');
                        if($input.attr('name') in errors) {
                            $group.addClass('has-error');
                            $group.append('<p class="help-block errormsg">' + errors[$input.attr('name')][0] + '</p>');
                        } else {
                            $group.addClass('has-success');
                        }
                    });
                }
            }
            $.ajaxSetup({
                headers : {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                method  : "GET",
                dataType: "json"
            });
            $('select[select2]').each(function () {
                $(this).select2({placeholder: $(this).attr('select2') || ''})
            });
            @yield('scripts')
        </script>
    </body>
</html>