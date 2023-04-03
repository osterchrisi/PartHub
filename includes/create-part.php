<?php
// Creates new row in the parts table with standard / temp values and returns the new ID
//! This could use a better temp name because the name has to be unique and I already forsee problems

include '../config/credentials.php';
include 'SQL.php';

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$stmt = $conn->prepare("INSERT INTO parts
                        (part_id, part_name, part_description, part_comment, created_at, part_category_fk, part_footprint_fk, part_unit_fk, part_owner_u_fk, part_owner_g_fk) VALUES
                        (NULL,:part_name,NULL,NULL,DEFAULT,DEFAULT,DEFAULT,DEFAULT,1,NULL)");
$stmt->bindParam(':temp_name', $temp_name);
$stmt->execute();
$new_id = $conn->lastInsertId();
// $name = $name[0]['user_name'];
echo json_encode(array('id' => $new_id));