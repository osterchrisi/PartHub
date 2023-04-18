<?php
/*
 * Get Categories for further processing in Parts table
 */

include 'session.php';
include '../config/credentials.php';
include 'SQL.php';

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$categories = getCategories($conn, $_user_id);
$r = json_encode($categories);
echo $r;