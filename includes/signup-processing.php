<?php
require_once __DIR__ . '/../vendor/autoload.php';
include '../config/credentials.php';
include 'SQL.php';
include 'helpers.php';

// Verify captcha
$secret = getenv('G_RECAPTCHA_SECRET');
$gRecaptchaResponse = $_POST['g-recaptcha-response'];
$remoteIp = $_SERVER['REMOTE_ADDR'];

$recaptcha = new \ReCaptcha\ReCaptcha($secret);
$resp = $recaptcha->verify($gRecaptchaResponse, $remoteIp);

if ($resp->isSuccess()) {
    // Captcha verified
    $conn = connectToSQLDB($hostname, $username, $password, $database_name);

    // Sanitize e-mail
    $email = $_POST['email'];
    $email = sanitizeInput($email);
    $email = sanitizeString($email);

    // Sanitize user name
    $user_name = $_POST['user_name'];
    $user_name = sanitizeInput($user_name);
    $user_name = sanitizeString($user_name);

    // Check if a user with that e-mail already exists
    if (validateEmail($email) && checkIfUserExists($conn, $email) > 0) {
        header("Location: /PartHub/pages/signup.php?ue");
        exit();
    }

    if (validateEmail($email) && checkIfUserExists($conn, $email) == 0) {
        $passwd = $_POST['passwd'];
        $passwd = password_hash($passwd, PASSWORD_ARGON2I);
        $user_id = createUser($conn, $email, $passwd, $user_name);
        $subject = 'Welcome to PartHub!';
        $body = 'Thank you for chosing PartHub, it\'s great!';
        $altbody = 'Thank you for chosing PartHub, it\'s great!';
        include 'sendmail.php';
    }
}
else {
    echo "reCAPTCHA NOT verified!";
    header("Location: /PartHub/pages/signup.php?cnv");
    exit();
}