<?php
$basename = basename(__FILE__);
$title = 'CSV Import Server Side';
require_once 'head.html';
include '../config/credentials.php';
include 'SQL.php';
include 'navbar.php';
// Retrieve the uploaded CSV file
$csvFile = $_FILES['csvFile']['tmp_name'];

// // Open the CSV file and read the data
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    echo "<pre>";
    while (($data = fgetcsv($handle)) !== false) {
        // Display each row of data
        print_r($data);
    }

    // Close the CSV file
    fclose($handle);


    //     try {
//     $conn = connectToSQLDB($hostname, $username, $password, $database_name);
//     $sql = "INSERT INTO your_table_name (column1, column2, column3)
//            VALUES (?, ?, ?)";
//     $stmt = $conn->prepare($sql);

    //     // Loop through each row in the CSV file
//     while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
//         // Bind the parameters to the SQL statement
//         $stmt->bindParam(1, $data[0]);
//         $stmt->bindParam(2, $data[1]);
//         $stmt->bindParam(3, $data[2]);

    //         $stmt->execute();
//         echo "done";
//     }
//     } catch (Exception $e) {
//     echo "<br>Error: " . $e->getMessage();
//   }
}