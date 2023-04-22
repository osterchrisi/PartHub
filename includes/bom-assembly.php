<?php
/**
 * @file This script is not documented yet
 */

include 'session.php';
include '../config/credentials.php';
include 'SQL.php';

$ids = $_POST['ids'];
$assemble_quantity = $_POST['quantity'];
$from_location = $_POST['from_location'];

$conn = connectToSQLDB($hostname, $username, $password, $database_name);

$success = 0;
$fail = 0;

// It's an array even if it has just one entry, so I need to iterate here
foreach ($ids as $bom_id) {
    try {
        //TODO: Make new query here without joining parts - I don't actually need the extra info
        $elements = getBomElements($conn, $bom_id);

        // Iterating over BOM elements (parts)
        foreach ($elements as $element) {
            $element_quantity = $element['element_quantity'];
            $part_id = $element['part_id'];
            $reducing_quantity = $assemble_quantity * $element_quantity;

            //TODO: Not super happy with getting these here and putting it into the
            //TODO: $_SESSION array but did this earlier for stockChanges.php 
            // Get stock levels in available locations
            $stock_levels = getStockLevels($conn, $part_id);

            // Putting stock levels into the session array for stockChanges.php to use
            $_SESSION['stock_levels'] = $stock_levels;

            // Prepare POST array
            $data = array(
                'change' => '-1',
                'quantity' => $reducing_quantity,
                'to_location' => NULL,
                'from_location' => $from_location,
                'comment' => 'some meaningful comment',
                'part_id' => $part_id,
                'permission' => false
            );
            $data_string = http_build_query($data); // Encode into URL string
            parse_str($data_string, $_POST);

            // Perform stock changes
            include 'stockChanges.php';
        }
        // $success += 1;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage(), "\n";
        $fail += 1;
    }
}

//TODO: Properly return successful and failed assemblies