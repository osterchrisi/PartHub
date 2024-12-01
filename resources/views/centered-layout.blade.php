@include('header')
@include('navbar')

<div
    class="d-flex flex-grow-1 justify-content-center align-items-center {{ isset($view) && in_array($view, ['whatis', 'signup', 'verify-mail', 'Forgot Password', 'register-testing', 'welcome', 'support']) ? 'landing-page' : '' }}">
    <div class="greeting d-flex align-items-center">
        @yield('content')
    </div>
</div>

@include('footer')

@yield('modals and menus')

</body>

</html>
