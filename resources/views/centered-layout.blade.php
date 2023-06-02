@include('header')
@include('navbar')

<div class="d-flex flex-grow-1 justify-content-center align-items-center">
    <div class="greeting d-flex align-items-center">
        @yield('content')
    </div>
</div>

@yield('modals and menus')

</body>
</html>

<style>
    #welcome-headline::after {
        content: 'BETA';
        font-size: 12px;
        vertical-align: top;
    }
</style>

<script>
    $(document).ready(function() {
        continueAsDemoUser();
    });
</script>
