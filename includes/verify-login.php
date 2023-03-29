<?php
include '../config/credentials.php';
include 'SQL.php';

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$hash = getPasswordHash($conn, $_POST['email']);

//! Not really happy with getting too much info at once? Maybe only password hash?
if (password_verify($_POST['passwd'], $hash[0]['user_passwd'])){
    $user = $hash[0]['user_name'];
    echo "password correct, welcome $user";
}
else {
    echo "password incorrect";
}