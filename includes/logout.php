<?php
$basename = basename(__FILE__);
include 'session.php';
$_SESSION = array();

// delete the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}
session_destroy();
header('Location: /PartHub/index.php?logout');
exit();