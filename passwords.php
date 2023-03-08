<?php
// Example for hashing and verifying passwords:
$input_password = 'my_password';
$stored_hash = '$2y$10$WNPoCzNW0aEmYhZZmdwae.20bgiCzt/UCtyUTz4C4O9RGWzN2sMfO';
if (password_verify($input_password, $stored_hash)) {
    echo 'Password is valid!';
} else {
    echo 'Invalid password.';
}


// also check out:
session_start();
// and
$_SESSION['user_id'] = $user_id;
