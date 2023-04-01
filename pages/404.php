<?php
// 404 - not found!
$basename = basename(__FILE__);
$title = 'Page not found :(';
require_once'../includes/head.html';
include '../config/credentials.php';
include '../includes/SQL.php';
require_once'../includes/navbar.php';
?>

<div class="d-flex align-items-center justify-content-center vh-100">
    <div class="text-center">
        <h1 class="display-1 fw-bold">404</h1>
        <p class="fs-3"> <span class="text-danger">Whoopsie!</span> Page not found.</p>
        <p class="lead">The page you're looking for doesn't exist :(</p>
    </div>
</div>
</body>

</html>