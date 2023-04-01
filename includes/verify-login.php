<?php
include '../config/credentials.php';
include 'SQL.php';
include 'helpers.php';

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$user = getPasswordHash($conn, $_POST['email']);

//! Not really happy with getting too much info at once? Maybe only password hash?
//! What if user is malicious?
if (password_verify($_POST['passwd'], $user[0]['user_passwd'])) {
    session_destroy();
    session_set_cookie_params(0, '/', '', true, true);
    session_start();

    $_SESSION['user_id'] = $user[0]['user_id'];
    header("Location: /PartHub/index.php?login");
}
else {
    echo "<br>password incorrect";
}