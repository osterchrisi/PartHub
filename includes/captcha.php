<?php
require_once __DIR__ . '/../vendor/autoload.php';
include '../config/credentials.php';
include 'SQL.php';
include 'helpers.php';

$secret = getenv('G_RECAPTCHA_SECRET');
$gRecaptchaResponse = $_POST['g-recaptcha-response'];
$remoteIp = $_SERVER['REMOTE_ADDR'];

$recaptcha = new \ReCaptcha\ReCaptcha($secret);
$resp = $recaptcha->verify($gRecaptchaResponse, $remoteIp);

if ($resp->isSuccess()) {
    echo "reCAPTCHA verified!";

    $conn = connectToSQLDB($hostname, $username, $password, $database_name);

    $email = $_POST['email'];
    $email = sanitizeInput($email);
    $email = sanitizeString($email);

    if (validateEmail($email) && checkIfUserExists($conn, $email) > 0) {
        echo "User with that e-email already exists!";
    }

    if (validateEmail($email) && checkIfUserExists($conn, $email) == 0) {
        $passwd = $_POST['passwd'];
        $passwd = sanitizeString($passwd);
        echo "<br>Sign up user with email: $email and password: $passwd";
    }
}
else {
    echo "reCAPTCHA NOT verified!";
    $errors = $resp->getErrorCodes();
    // I don't know... send him back??
}