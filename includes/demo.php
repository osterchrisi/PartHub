<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(0, '/', '', true, true);
    session_start();
}
//! The below version is really want I want to work for code maintainability
//! but for some reason it doesn't works
// $basename = basename(__FILE__);
// include '/PartHub/includes/session.php';

$demo_user_id = '-1'; //demo-user
$_SESSION['user_id'] = $demo_user_id;
echo json_encode(array($_SESSION, $basename));
?>