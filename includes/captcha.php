<?php
require_once __DIR__ . '/../vendor/autoload.php';

$secret = getenv('G_RECAPTCHA_SECRET');
$gRecaptchaResponse = $_POST['g-recaptcha-response'];
$remoteIp = $_SERVER['REMOTE_ADDR'];

$recaptcha = new \ReCaptcha\ReCaptcha($secret);
$resp = $recaptcha->verify($gRecaptchaResponse, $remoteIp);

if ($resp->isSuccess()) {
    echo "reCAPTCHA verified!";
    // Sign up user
}
else {
    echo "reCAPTCHA NOT verified!";
    $errors = $resp->getErrorCodes();
    // I don't know... send him back??
}