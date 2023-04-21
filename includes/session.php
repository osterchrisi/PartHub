<?php
// Initiate session or not initiate sesstion - that is the question!

// Start session if not started yet
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(0, '/', '', true, true);
    session_start();
}

// Do not redirect on these pages
$doNothingArray = ['login.php',
                    'demo.php',
                    // 'parts-info.php', //if I keep it in, it doesn't have access to the user_id for locations
                    'stockModals.php',
                    'show-bom.php',
                    'stock-history.php',
                    'signup.php',
                    'signup-confirmation.php',
                    'pricing.php'];

// Do nothing
if (in_array($basename, $doNothingArray)) {
    return;
}

// This is not index.php
if ($basename != 'index.php') {
    // User already logged in
    //? Think this if statement is useless but feels good
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
    // Make him click one of the buttons by showing the modal
    else {
        if ($_GET['redirect']) {
            $show_modal = 1;
        }
    }

}
?>