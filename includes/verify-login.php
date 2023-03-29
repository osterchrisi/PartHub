<?php
include '../config/credentials.php';
include 'SQL.php';

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$hash = getPasswordHash($conn, $_POST['email']);

if (password_verify($_POST['passwd'], $hash[0]['user_passwd'])){
    echo "password correct";
}
else {
    echo "password incorrect";
}