<?php
require_once __DIR__ . '/../vendor/autoload.php';
include '../config/credentials.php';
include 'SQL.php';

$secret = getenv('G_RECAPTCHA_SECRET');
$gRecaptchaResponse = $_POST['g-recaptcha-response'];
$remoteIp = $_SERVER['REMOTE_ADDR'];

$recaptcha = new \ReCaptcha\ReCaptcha($secret);
$resp = $recaptcha->verify($gRecaptchaResponse, $remoteIp);

if ($resp->isSuccess()) {
    echo "reCAPTCHA verified!";
    $conn = connectToSQLDB($hostname, $username, $password, $database_name);
    if(checkIfUserExists($conn, $_POST['email']) > 0){
        echo "User with that e-mail already exists!";
    }
}
else {
    echo "reCAPTCHA NOT verified!";
    $errors = $resp->getErrorCodes();
    // I don't know... send him back??
}