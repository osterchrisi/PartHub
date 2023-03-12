<?php
/*
 * Get Categories for further processing in Parts table
 */


// ! Strangely, I need to 'echo' the value and not 'return' it - otherwise it won't work?!
include '../config/credentials.php';
include 'SQL.php';

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$categories = getCategories($conn);
$r = json_encode($categories);
echo $r;
// return $r;