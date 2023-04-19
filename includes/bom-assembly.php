<?php
/**
 * @file This script is not documented yet
 */

include '../config/credentials.php';
include 'SQL.php';
$ids = $_POST['ids'];
$quantity = $_POST['quantity'];
$from_location = $_POST['from_location'];

$conn = connectToSQLDB($hostname, $username, $password, $database_name);

$success = 0;
$fail = 0;

echo var_dump($_POST);

// It's an array even if it has just one entry, so I need to iterate here
foreach ($ids as $bom_id) {
    try {
        // deleteRowById($conn, $id, $table, $column);
        echo "Assembling $quantity times BOM with ID: $bom_id\n";
        //TODO: Make new query here without joining parts - I don't actually need the extra info
        $elements = getBomElements($conn, $bom_id);
        echo "These are the elements:\n";
        echo print_r($elements);

        foreach ($elements as $element) {
            $element_quantity = $element['element_quantity'];
            $part_id = $element['part_id'];
            $reducing_quantity = $quantity * $element_quantity;

            echo "Reducing part ID $part_id by $reducing_quantity because element quantity is: $element_quantity\n";

            $data = array(
                'change' => '-1',
                'quantity' => $reducing_quantity,
                'to_location' => NULL,
                'from_location' => $from_location,
                'comment' => 'some meaningful comment',
                'part_id' => $part_id
            );
            $data_string = http_build_query($data); // Encode into URL string
            $_POST = array();
            parse_str($data_string, $_POST);
            include 'stockChanges.php';

        }

        // calculate reducing quantity (element quantity times quantity)
        // call stockChanges with POST: (-1 (reduce), reducing_quantity, to_location (NULL), from_location (selected location), comment (BOM Build), user_id, part_id)


        // $success += 1;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage(), "\n";
        $fail += 1;
    }
}

//TODO: Properly return successful and failed assemblies