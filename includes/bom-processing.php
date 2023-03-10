<?php
require_once('navbar.php');
include '../config/credentials.php';
include 'SQL.php';

// Get all the variables from the entry script
$bom_name = $_POST['bom_name'];
$dynamicFields = $_POST['dynamic_field'];
var_dump($dynamicFields);


if ($bom_name) {
    try {
    // Connect to database
    $conn = connectToSQLDB($hostname, $username, $password, $database_name);

    // First, insert new BOM
    $new_id = createBom($conn, $bom_name, $bom_description);
    }
    catch (Exception $e) {
        echo "<br>Error: " . $e->getMessage();
    }

    echo "BOM Name: $bom_name";

    // Extract the 'amount x part' pairs from the array / JSON into $value1, $value2, ...
    // Where all odd values are part_ids and all even values are the amounts
    $i = 1;
    foreach ($dynamicFields as $key => $value) {
        ${'value'.$i} = $value;
        if ($i % 2 == 0) { // Every second row. That's because every second row is one full amount x part pair
            // echo "${'value'.$i} times ${'value'.($i-1)}<br>";
            $amount = ${'value'.$i};
            $part_id = ${'value'.($i-1)};
            try { 
            insertBomElements($conn, $new_id, $part_id, $amount);
            }
            catch (Exception $e) {
                echo "<br>Error: " . $e->getMessage();
            }
        }
        $i++;
    }

    // Redirect to showing the newly created backorder
    header("Location: ../pages/show-bom.php?id=" . urlencode($new_id));
    exit;
}
else {echo "You didn't enter a BOM name";}
?>
