<?php
require_once __DIR__ . '/../vendor/autoload.php';
include '../config/credentials.php';
include 'SQL.php';


if (isset($_POST['submit'])) {
    // Call your PHP function here
    processForm($_POST['email'], $_POST['passwd']);
}

function processForm($email, $passwd)
{
echo "email: $email<br>";
echo "passwd: $passwd<br>";

// Sanitize a user input string by removing leading/trailing white spaces and HTML special characters
function sanitizeString($input) {
    $input = trim($input);
    $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return $input;
}

// Sanitize a user input string by stripping out potentially dangerous characters
function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = filter_var($input, FILTER_SANITIZE_STRING);
    return $input;
}

$e1 = sanitizeString($email);
$e2 = sanitizeInput($e1);
echo "e1: $e1<br>";
echo "e2: $e2<br>";

$p1 = sanitizeString($passwd);
$p2 = sanitizeInput($p1);
echo "p1: $p1<br>";
echo "p2: $p2<br>";

function validateEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'false';
    }
    return 'true';
}

$m = validateEmail($e2);
echo "Die mail ist: $m";

// Check if a string is within a certain length range
function checkStringLength($input, $min, $max) {
    $length = strlen($input);
    if ($length < $min || $length > $max) {
        return false;
    }
    return true;
}

}
?>
<form action="" method="post">
    <input class="form-control" name="email" placeholder="e-mail"><br>
    <input class="form-control" name="passwd" placeholder="password"><br>
    <!-- <button class="btn btn-primary" type="submit" name="submit"> -->
    <input type="submit" name="submit" value="Submit">
</form>