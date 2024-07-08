@include('header')
@include('navbar')

<div class="d-flex flex-grow-1 justify-content-center align-items-center {{ $view == 'whatis' || 'signup' ? 'landing-page' : '' }}">
    <div class="greeting d-flex align-items-center">
        @yield('content')
    </div>
</div>

@include('footer')

@yield('modals and menus')

</body>
</html>