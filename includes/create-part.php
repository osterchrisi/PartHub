<?php
include 'session.php';
include '../config/credentials.php';
include 'SQL.php';

// Gather variables
$part_name = $_POST['part_name'];
$quantity = $_POST['quantity'];
$to_location = $_POST['to_location'];
$user_id = $_SESSION['user_id'];

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$stmt = $conn->prepare("INSERT INTO parts
                        (part_id, part_name, part_description, part_comment, created_at, part_category_fk, part_footprint_fk, part_unit_fk, part_owner_u_fk, part_owner_g_fk) VALUES
                        (NULL,:part_name,NULL,NULL,DEFAULT,DEFAULT,DEFAULT,DEFAULT,:user_id,NULL)");
$stmt->bindParam(':part_name', $part_name, PDO::PARAM_STR);
$stmt->bindParam(':user_id', $user_id);

$stmt->execute();
$new_part_id = $conn->lastInsertId();






echo json_encode(array('id' => $new_part_id, 'user_id' => $user_id));