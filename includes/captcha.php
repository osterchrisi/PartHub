<?php
//! turn this into an OS variable?
$recaptcha_secret = "6Lca_UAlAAAAANcRgODTZlV5Slu0w4OTC-TrmMui";
$recaptcha_response = $_POST['recaptcha_response'];
$recaptcha_url = "https://www.google.com/recaptcha/api/siteverify";
$recaptcha_data = array('secret' => $recaptcha_secret, 'response' => $recaptcha_response);
$recaptcha_options = array('http' => array('method' => 'POST', 'content' => http_build_query($recaptcha_data)));
$recaptcha_context = stream_context_create($recaptcha_options);
$recaptcha_result = file_get_contents($recaptcha_url, false, $recaptcha_context);
$recaptcha_result = json_decode($recaptcha_result, true);

var_dump($recaptcha_response);

if ($recaptcha_result['success'] == true) {
    echo "verified";
} else {
    echo "not verified";
}
?>