<?php
// Testing page that sends mails to myself, so I can check how they look
require_once __DIR__ . '/../vendor/autoload.php';
include '../config/credentials.php';
include 'SQL.php';
include 'helpers.php';

// Get this from the DB
$email = 'christian@koma-elektronik.com';
$user_name = 'Christian Zollner';

// Create template for this
$subject = 'Welcome to PartHub!';
$body = file_get_contents('../assets/mail_templates/welcome.html');;
$altbody = 'Thank you for chosing PartHub, it\'s great!';

// Replace placeholders in template
$body = str_replace('{{name}}', $user_name, $body);

// Send mail
include 'sendmail.php';