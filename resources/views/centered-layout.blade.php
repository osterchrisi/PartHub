@include('header')
@include('navbar')

<div
    class="d-flex flex-grow-1 justify-content-center align-items-center {{ in_array($view, ['whatis', 'signup', 'verify-mail', 'Forgot Password']) ? 'landing-page' : '' }}">
    <div class="greeting d-flex align-items-center">
        @yield('content')
    </div>
</div>

@include('footer')

@yield('modals and menus')

</body>

</html>
