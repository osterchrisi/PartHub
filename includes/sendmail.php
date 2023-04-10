<?php
// PHPMailer function. Expects a few variables and will send an e-mail

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require '../vendor/autoload.php';

function sendEmail($email, $user_name, $subject, $body, $alt_body)
{
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    $result = null;

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; //Enable verbose debug output
        $mail->isSMTP(); //Send using SMTP
        $mail->Host = 'smtp.ionos.de'; //Set the SMTP server to send through
        $mail->SMTPAuth = true; //Enable SMTP authentication
        $mail->Username = 'hello@parthub.online'; //SMTP username
        $mail->Password = getenv('SMTP_KEY'); //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; //Enable implicit TLS encryption
        $mail->Port = 465; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('hello@parthub.online', 'PartHub');
        // $mail->addAddress('joe@example.net', 'Joe User');        //Add a recipient
        $mail->addAddress($email, $user_name); //Name is optional

        //Content
        $mail->isHTML(true); //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $alt_body;

        $mail->send();
        echo 'Message has been sent';
        $result = 'success';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        $result = 'failure';
    }

    return $result;
}