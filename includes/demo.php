<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$demo_user_id = '1'; //chrisi!
$_SESSION['user_id'] = '1';
header('Location: /PartHub/index.php');
exit();
?>