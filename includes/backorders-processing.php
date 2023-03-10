<?php
require_once('includes/navbar.php');
include 'config/credentials.php';
include 'includes/SQL.php';

// Get all the variables from the entry script
$customer_id = $_POST['customer_id'];
$customer_po = $_POST['customer_po'];
$dynamicFields = $_POST['dynamic_field'];

if ($customer_po) {
    try {
    // Connect to database
    $conn = connectToSQLDB($hostname, $username, $password, $database_name);

    // First, insert new backorder with customer_id and customer_po
    $new_id = createBackorder($conn, $customer_id, $customer_po);
    }
    catch (Exception $e) {
        echo "<br>Error: " . $e->getMessage();
    }

    echo "Customer: $customer_id";
    echo "<br>";
    echo "<br>";
    echo "Customer PO: $customer_po";

    // Extract the 'Amount x Product' pairs from the array / JSON into $value1, $value2, ...
    // Where all odd values are product IDs and all even values are the amounts
    $i = 1;
    foreach ($dynamicFields as $key => $value) {
        ${'value'.$i} = $value;
        if ($i % 2 == 0) { // Every second row. That's because every second row is one full Amount x Product pair
            // echo "${'value'.$i} times ${'value'.($i-1)}<br>";
            $amount = ${'value'.$i};
            $product_id = ${'value'.($i-1)};
            try { 
            insertBackorderProducts($conn, $new_id, $product_id, $amount);
            }
            catch (Exception $e) {
                echo "<br>Error: " . $e->getMessage();
            }
        }
        $i++;
    }

    // Redirect to showing the newly created backorder
    header("Location: pages/show-backorder.php?po=" . urlencode($customer_po));
    exit;
}
else {echo "You didn't enter a PO number";}
?>
