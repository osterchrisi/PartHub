<?php
// Start session if not started yet
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(0, '/', '', true, true);
    session_start();
}

if ($basename == 'login.php' || $basename == 'demo.php'){
    return;
}
// This is not index.php

if ($basename != 'index.php') {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }
    // User not logged in and not index.php, so redirect him there
    else {
        header("Location: /PartHub/index.php?redirect=1");
    }
}
// This is index.php
else {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }
    // Make him click one of the buttons
    else {
        if ($_GET['redirect']) {
            $show_modal = 1;
        }
    }

}
?>