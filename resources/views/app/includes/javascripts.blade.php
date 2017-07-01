<script src="/js/vendors.js"></script>
<script src="/js/app.js"></script>
@yield('javascripts')
{!! Notifications::config() !!}
<script>
    @yield('scripts')
</script>