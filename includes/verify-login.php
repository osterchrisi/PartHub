<?php
include '../config/credentials.php';
include 'SQL.php';

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$hash = getPasswordHash($conn, $_POST['email']);

//! Not really happy with getting too much info at once? Maybe only password hash?
if (password_verify($_POST['passwd'], $hash[0]['user_passwd'])) {
    session_destroy();
    session_set_cookie_params(0, '/', '', true, true);
    session_start();

    $_SESSION['user_id'] = $hash[0]['user_id'];
    header("Location: /PartHub/index.php");
}
else {
    echo "password incorrect";
}