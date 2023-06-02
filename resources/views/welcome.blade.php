@include('header')
@include('navbar')


<style>
    h1.display-1::after {
        content: 'BETA';
        font-size: 12px;
        vertical-align: top;
    }
</style>

<div class="d-flex flex-grow-1 justify-content-center align-items-center">
    <div class="greeting d-flex align-items-center">
        <table class="table table-borderless text-center mx-auto w-auto" style="borders: false">
            <tr>
                <td colspan="3">
                    <h1 class="display-1">PartHub</h1><br>
                    <h1>Inventory and BOM management</h1><br>
                    @if (session('loggedIn'))
                        <div class="alert alert-info" role="alert">Nice to have you back,
                            {{ optional(Auth::user())->name }}!</div>
                    @endif

                    Hello internet stranger that has found their way to PartHub!<br>
                    PartHub is <strong>not yet fully functional</strong> but many parts do work.<br><br>
                    So, if you like, start looking at some parts, create a BOM, move some stock and just see what
                    happens. Go
                    ahead and explore!
                    <br>The database will reset every full hour, then all your changes will be lost :(<br><br>
                </td>
            </tr>
            <tr>
                <td><a href="{{ route('parts') }}">
                        <h1><i class="bi bi-cpu"></i></h1>Parts<br><br>
                    </a></td>
                <td><a href="{{ route('boms') }}">
                        <h1><i class="bi bi-clipboard-check"></i></h1>BOMs<br>
                    </a></td>
                <td><a href="{{ route('locations') }}">
                        <h1><i class="bi bi-buildings"></i></h1>Storage<br>
                    </a></td>
            </tr>
            <tr>
                <td><a href="{{ route('categories') }}">
                        <h1><i class="bi bi-boxes"></i></h1>Categories
                    </a></td>
                <td><a href="{{ route('suppliers') }}">
                        <h1><i class="bi bi-cart2"></i></h1>Suppliers
                    </a></td>
                <td><a href="{{ route('footprints') }}">
                        <h1><i class="bi bi-outlet"></i></h1>Footprints
                    </a></td>
            </tr>
            @unless (auth()->check())
                <tr>
                    <td colspan="3">
                        <table class="table table-borderless">
                            <tbody class="alert alert-danger">
                                <tr>
                                    <td><button type="button" class="btn btn-primary" id="continueDemo">Continue as demo
                                            user</button></td>
                                    <td>
                                        <form id="loging-button" action="{{ route('login') }}" method="GET">
                                            @csrf<button type="submit" class="btn btn-primary" id="logIn">Log into your
                                                account</button></form>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endunless
        </table>
    </div>
</div>
</div>

<!-- The user stuff modal -->
<div id="myModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">User stuff</h4>
            </div>
            <div class="modal-body">
                <p>You are currently not logged in.</p>
                <br>
                <p>Please continue as demo user or log in!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

</body>

</html>

<?php
// Show the login modal if user is not logged in yet
if (isset($show_modal) && $show_modal == 1) {
    echo "<script>var myModal = new bootstrap.Modal(document.getElementById('myModal'));</script>";
    echo '<script>myModal.show();</script>';
}
?>

<script>
    $(document).ready(function() {
        continueAsDemoUser();
    });
</script>
