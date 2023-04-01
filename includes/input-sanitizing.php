<?php
require_once __DIR__ . '/../vendor/autoload.php';
include '../config/credentials.php';
include 'SQL.php';
include 'helpers.php';


if (isset($_POST['submit'])) {
    // Call your PHP function here
    processForm($_POST['email'], $_POST['passwd']);
}

function processForm($email, $passwd)
{
echo "email: $email<br>";
echo "passwd: $passwd<br>";
$hash = password_hash($passwd, PASSWORD_ARGON2I);
echo "hash: $hash";

$e1 = sanitizeString($email);
$e2 = sanitizeInput($e1);
echo "e1: $e1<br>";
echo "e2: $e2<br>";

$p1 = sanitizeString($passwd);
$p2 = sanitizeInput($p1);
echo "p1: $p1<br>";
echo "p2: $p2<br>";

$m = validateEmail($e2);
echo "Die mail ist: $m";



}
?>
<form action="" method="post">
    <input class="form-control" name="email" placeholder="e-mail"><br>
    <input class="form-control" name="passwd" placeholder="password"><br>
    <!-- <button class="btn btn-primary" type="submit" name="submit"> -->
    <input type="submit" name="submit" value="Submit">
</form>