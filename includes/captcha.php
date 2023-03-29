<?php
require_once __DIR__ . '/../vendor/autoload.php';

$secret = "6Lca_UAlAAAAANcRgODTZlV5Slu0w4OTC-TrmMui";
$gRecaptchaResponse = $_POST['g-recaptcha-response'];
$remoteIp = $_SERVER['REMOTE_ADDR'];

$recaptcha = new \ReCaptcha\ReCaptcha($secret);
$resp = $recaptcha->verify($gRecaptchaResponse, $remoteIp);

if ($resp->isSuccess()) {
    echo "reCAPTCHA verified!";
}
else {
    echo "reCAPTCHA NOT verified!";
    $errors = $resp->getErrorCodes();
    // add your code here
}