<?php
/**
 * @file This script is not documented yet
 */

include 'session.php';
include '../config/credentials.php';
include 'SQL.php';

$ids = $_POST['ids'];
$assemble_quantity = $_POST['assemble_quantity'];
$from_location = $_POST['from_location'];

$conn = connectToSQLDB($hostname, $username, $password, $database_name);

// The array for all aquired stock changes to go in
$all_stock_changes = array();

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

            // Prepare POST array
            $stock_change = array(
                'bom_id' => $bom_id,
                'part_id' => $part_id,
                'change' => '-1',
                'quantity' => $reducing_quantity,
                'to_location' => NULL,
                'from_location' => $from_location,
                'comment' => 'some meaningful comment',
                // 'permission' => false
            );

            $all_stock_changes[] = $stock_change;


        }
        // $success += 1;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage(), "\n";
        $fail += 1;
    }
}

// Assign the final array to the stock_changes key in the $_POST array
$_POST['stock_changes'] = $all_stock_changes;

// Encode the data and send the POST request
$data_string = http_build_query($_POST);

// Perform stock changes
include 'prepareStockChanges.php';