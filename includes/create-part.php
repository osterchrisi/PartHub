<?php
include 'session.php';
include '../config/credentials.php';
include 'SQL.php';

// Gather variables
$part_name = $_POST['part_name'];
$quantity = $_POST['quantity'];
$to_location = $_POST['to_location'];
$comment = $_POST['comment'];
$user_id = $_SESSION['user_id'];

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$stmt = $conn->prepare("INSERT INTO parts
                        (part_id, part_name, part_description, part_comment, created_at, part_category_fk, part_footprint_fk, part_unit_fk, part_owner_u_fk, part_owner_g_fk) VALUES
                        (NULL,:part_name,NULL,NULL,DEFAULT,DEFAULT,DEFAULT,DEFAULT,:user_id,NULL)");
$stmt->bindParam(':part_name', $part_name, PDO::PARAM_STR);
$stmt->bindParam(':user_id', $user_id);

$stmt->execute();
$new_part_id = $conn->lastInsertId();

// Make stock entry
$new_stock_entry_id = stockEntry($conn, $new_part_id, $to_location, $quantity);

// Create stock level change history entry
$new_stock_level_id = stockChange($conn, $new_part_id, NULL, $to_location, $quantity, $comment, NULL, $user_id);

echo json_encode(array('id' => $new_part_id, 'sl_di' => $new_stock_entry_id, 'sl_chng_id' => $new_stock_level_id));