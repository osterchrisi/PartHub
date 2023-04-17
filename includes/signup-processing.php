<?php
// Processes POST array from the signup page
require_once __DIR__ . '/../vendor/autoload.php';
include '../config/credentials.php';
include 'SQL.php';
include 'helpers.php';
include 'sendmail.php';

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
        header("Location: /PartHub/pages/signup.php?ue"); // ue = user exists
        exit();
    }

    if (validateEmail($email) && checkIfUserExists($conn, $email) == 0) {
        // Create new account
        $passwd = $_POST['passwd'];
        $passwd = password_hash($passwd, PASSWORD_ARGON2I);
        $user_id = createUser($conn, $email, $passwd, $user_name);

        // Send confirmation e-mail
        $subject = 'Welcome to PartHub!';
        $template = file_get_contents('../assets/mail_templates/welcome.html');

        // Replace name and e-mail address
        $data = array(
            '{{name}}' => $user_name,
            '{{mail-address}}' => $email
        );
        $body = str_replace(array_keys($data), array_values($data), $template);

        // Same for alt body
        $alt_body = 'Thank you for chosing PartHub, {{name}}! our user account has been created and you can start adding parts and BOMs right away. All the best, the PartHub team from Berlin';
        $alt_body = str_replace(array_keys($data), array_values($data), $template);

        // Send mail
        $result = sendEmail($email, $user_name, $subject, $body, $alt_body);
        echo "<br>$result";

        if ($result == 'success') {
            // Display confirmation
            echo "br>success";
            echo "<script>window.location.href='/PartHub/pages/signup-confirmation.php';</script>";
        }
        else {
            echo "Something went wrong sending the confirmation e-mail but you still got signed up";
        }

    }
}
else {
    echo "reCAPTCHA NOT verified!";
    header("Location: /PartHub/pages/signup.php?cnv"); // cnv = captcha not verified
    exit();
}