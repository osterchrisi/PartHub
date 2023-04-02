<?php
// Log out the user and take him back to the landing page
$basename = basename(__FILE__);
include 'session.php';

// Empty the $_SESSION array
$_SESSION = array();

// Delete the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Destroy the session
session_destroy();

// Go back to index and show that you just logged out
header('Location: /PartHub/index.php?logout');
exit();