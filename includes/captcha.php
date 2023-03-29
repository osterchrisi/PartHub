<?php
$recaptcha_secret = "YOUR_SECRET_KEY";
$recaptcha_response = $_POST['recaptcha_response'];
$recaptcha_url = "https://www.google.com/recaptcha/api/siteverify";
$recaptcha_data = array('secret' => $recaptcha_secret, 'response' => $recaptcha_response);
$recaptcha_options = array('http' => array('method' => 'POST', 'content' => http_build_query($recaptcha_data)));
$recaptcha_context = stream_context_create($recaptcha_options);
$recaptcha_result = file_get_contents($recaptcha_url, false, $recaptcha_context);
$recaptcha_result = json_decode($recaptcha_result, true);
if ($recaptcha_result['success'] == true) {
    // reCAPTCHA verification passed
} else {
    // reCAPTCHA verification failed
}
?>