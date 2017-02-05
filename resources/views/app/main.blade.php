<!DOCTYPE html>
<html lang="en">
    <head>
        @include('app.includes.head')
    </head>
    <body page-section="@yield('page-section')" page-id="@yield('page-id')">
        <!-- Persistent messages -->
        <div class="message-centre" id="message-centre-upper">
            <ul>
                @include('app.messages.fixed')
            </ul>
        </div>
        <!-- Main site wrapper -->
        <div id="site-wrapper">
            <!-- Header -->
            <div id="header-wrapper">
                <img src="/images/bts-logo.png">
            </div>
            <!-- Main menu -->
            <div id="menu-wrapper">
                @include('app.includes.menu')
            </div>
            <!-- Content -->
            <div id="content-wrapper">
                <!-- Main messages -->
                <div class="message-centre" id="message-centre-main">
                    <ul>
                        @yield('messages')
                        @include('app.messages.flash')
                    </ul>
                </div>
                @hasSection('header-main')
                    <h1 class="page-header">@yield('header-main')</h1>
                @endif
                @hasSection('header-sub')
                    <h2 class="page-header">@yield('header-sub')</h2>
                @endif
                <div id="content">
                    @yield('content')
                </div>
            </div>
        </div>
        <!-- Footer -->
        <div id="footer">
            <div id="footer-upper">
                @include('app.includes.footer_details')
            </div>
            <div class="copyright" id="footer-lower">
                @include('app.includes.footer_copyright')
            </div>
        </div>
        @include('app.includes.modal')
        @include('app.includes.javascripts')
    </body>
</html>