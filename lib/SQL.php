<?php
/*
 * SQL Library
 */

function connectToSQLDB($hostname, $username, $password, $database_name){
    // Connect to the database using PDO
    $conn = new PDO("mysql:host=$hostname;dbname=$database_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
}

function getColumnNames($conn, $table_name){
  // Select the column names of the table for the dropdown menu 
  $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?";
  $stmt = $conn->prepare($sql);
  $stmt->execute([$table_name]);
  $column_names = $stmt->fetchAll(PDO::FETCH_COLUMN);
  return $column_names;
}
function getTotalNumberOfRows($conn, $table_name, $search_column, $search_term, $column_names){
  // Get the total number of rows in the table, filtered by the search term
  $sql = "SELECT COUNT(*) as total FROM $table_name WHERE ";

  if ($search_column == 'everywhere') {
    // Search all columns
    $sql .= "CONCAT_WS(' ',";
    $num_columns = count($column_names);
    for ($i = 0; $i < $num_columns; $i++) {
      $column_name = $column_names[$i];
      $sql .= "$column_name";
      if ($i < $num_columns - 1) {
        $sql .= ", ";
      }
    }
    $sql .= ") LIKE :search_term ";
  } else {
    // Search only the specified column
    $sql .= "$search_column LIKE :search_term ";
  }
  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':search_term', "%$search_term%", PDO::PARAM_STR);
  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $total_rows = $row['total'];
  return $total_rows;
}

function queryDB($table_name, $search_column, $search_term, $offset, $results_per_page, $conn, $column_names){
  // Select a limited set of data from the table, based on the current page and number of results per page, filtered by the search term
  // $sql = "SELECT * FROM $table_name JOIN part_categories as part_categories ON parts.part_category_fk = part_categories.category_id WHERE ";
  $sql = "SELECT * FROM $table_name JOIN part_categories as part_categories ON parts.part_category_fk = part_categories.category_id WHERE ";

  if ($search_column == 'everywhere') {
    // Search all columns
    $sql .= "CONCAT_WS(' ',";
    $num_columns = count($column_names);
    for ($i = 0; $i < $num_columns; $i++) {
      $column_name = $column_names[$i];
      $sql .= "$column_name";
      if ($i < $num_columns - 1) {
        $sql .= ", ";
      }
    }
    $sql .= ") LIKE :search_term ";
  } else {
    // Search only the specified column
    $sql .= "$search_column LIKE :search_term ";
  }

  $sql .= "LIMIT :offset, :results_per_page";
  
  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':search_term', "%$search_term%", PDO::PARAM_STR);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  $stmt->bindValue(':results_per_page', $results_per_page, PDO::PARAM_INT);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $result;   
}

function backorder_query($table_name, $search_column, $search_term, $offset, $results_per_page, $conn, $column_names, $search_status){
  // Select a limited set of data from the table, based on the current page and number of results per page, filtered by the search term
  
  $sql = "SELECT customer_name, customer_po, created_at, product_name, amount, status_name FROM backorders JOIN customers ON backorders.customer_id = customers.id JOIN backorders_product_lookup ON backorders.id = backorders_product_lookup.backorder_id JOIN products ON backorders_product_lookup.product_id = products.id JOIN backorder_statuses ON backorders.backorder_status = backorder_statuses.id WHERE ";

  if ($search_column == 'everywhere') {
    // Search all columns
    $sql .= "CONCAT(customer_name, customer_po, created_at, product_name, status_name) LIKE :search_term ";
  } else {
    // Search only the specified column
    $sql .= "$search_column LIKE :search_term ";
  }

  $sql .= "AND backorder_status LIKE ";
  if ($search_status == 1) {
    $sql .= "1 ";
  } elseif ($search_status == 2) {
    $sql .= "2 ";
  } elseif ($search_status == 3) {
    $sql .= "3 ";
  } elseif ($search_status == 'all') {
    $sql .= "'%' ";
  }     

  $sql .= "LIMIT :offset, :results_per_page";
  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':search_term', "%$search_term%", PDO::PARAM_STR);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  $stmt->bindValue(':results_per_page', $results_per_page, PDO::PARAM_INT);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $result;   
}

function show_backorder_query($table_name, $search_column, $search_term, $offset, $results_per_page, $conn, $column_names, $search_status){
  // Select a limited set of data from the table, based on the current page and number of results per page, filtered by the search term
  
  $sql = "SELECT customer_name, customer_po, created_at, product_name, amount, status_name FROM backorders JOIN customers ON backorders.customer_id = customers.id JOIN backorders_product_lookup ON backorders.id = backorders_product_lookup.backorder_id JOIN products ON backorders_product_lookup.product_id = products.id JOIN backorder_statuses ON backorders.backorder_status = backorder_statuses.id WHERE $search_column = :search_term LIMIT :offset, :results_per_page";
  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':search_term', "$search_term", PDO::PARAM_STR);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  $stmt->bindValue(':results_per_page', $results_per_page, PDO::PARAM_INT);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $result;   
}

function getTotalNumberOfBackorderRows($conn, $table_name, $search_column, $search_term, $search_status){
  // Get the total number of rows in the table, filtered by the search term
  $sql = "SELECT customer_name, customer_po, created_at, product_name, amount, backorder_status FROM backorders JOIN customers ON backorders.customer_id = customers.id JOIN backorders_product_lookup ON backorders.id = backorders_product_lookup.backorder_id JOIN products ON backorders_product_lookup.product_id = products.id JOIN backorder_statuses ON backorders.backorder_status = backorder_statuses.id WHERE ";

  if ($search_column == 'everywhere') {
    // Search all columns
    $sql .= "CONCAT(customer_name, customer_po, created_at, product_name, backorder_status) LIKE :search_term ";
  } else {
    // Search only the specified column
    $sql .= "$search_column LIKE :search_term ";
  }

  $sql .= "AND backorder_status LIKE ";
  if ($search_status == 1) {
    $sql .= "1 ";
  } elseif ($search_status == 2) {
    $sql .= "2 ";
  } elseif ($search_status == 3) {
    $sql .= "3 ";
  } elseif ($search_status == 'all') {
    $sql .= "'%' ";
  }
  
  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':search_term', "%$search_term%", PDO::PARAM_STR);
  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  $total_rows = $stmt->rowCount();
  return $total_rows;
}

function getTotalNumberOfBomRows($conn, $table_name, $search_term){
  // Get the total number of rows in the table, filtered by the search term
  $sql = "SELECT bom_id, bom_name, bom_description FROM bom_names WHERE bom_name LIKE :search_term";
  
  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':search_term', "%$search_term%", PDO::PARAM_STR);
  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  $total_rows = $stmt->rowCount();
  return $total_rows;
}

function bom_query($conn, $table_name, $search_term, $offset, $results_per_page){
  // Select a limited set of data from the table, based on the current page and number of results per page, filtered by the search term
  
  $sql = "SELECT bom_id, bom_name, bom_description FROM bom_names WHERE bom_name LIKE :search_term LIMIT :offset, :results_per_page";
  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':search_term', "%$search_term%", PDO::PARAM_STR);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  $stmt->bindValue(':results_per_page', $results_per_page, PDO::PARAM_INT);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $result;   
}

function getBackordersCustomers($conn){
  $stmt = $conn->query("SELECT id, customer_name FROM customers");
  $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $customers;
}

function getBackordersProducts($conn){
  $stmt = $conn->query("SELECT id, product_name FROM products");
  $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $products;
}

function getAllParts($conn){
  $stmt = $conn->query("SELECT part_id, part_name FROM parts");
  $all_parts = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $all_parts;
}

function getAllBoms($conn){
  $stmt = $conn->query("SELECT bom_id, bom_name FROM bom_names");
  $boms = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $boms;
}

function getPartName($conn, $part_id){
  $sql = "SELECT part_name FROM parts WHERE part_id = :part_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':part_id', $part_id, PDO::PARAM_INT);
  $stmt->execute();
  $part_name = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $part_name;
}

function getBomName($conn, $bom_id){
  $sql = "SELECT bom_name FROM bom_names WHERE bom_id = :bom_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':bom_id', $bom_id, PDO::PARAM_INT);
  $stmt->execute();
  $bom_name = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $bom_name;
}
function createBackorder($conn, $customer_id, $customer_po){
  $stmt = $conn->prepare("INSERT INTO `backorders` (`id`, `customer_id`, `customer_po`, `created_at`) VALUES (NULL, :customer_id, :customer_po, current_timestamp())");
  $stmt->bindParam(':customer_id', $customer_id);
  $stmt->bindParam(':customer_po', $customer_po);
  $stmt->execute();

  $new_id = $conn->lastInsertId();
  return $new_id;
}

function createBom($conn, $bom_name, $bom_description=NULL){
  $stmt = $conn->prepare("INSERT INTO `bom_names` (`bom_id`, `bom_name`, `bom_description`) VALUES (NULL, :bom_name, :bom_description)");
  $stmt->bindParam(':bom_name', $bom_name);
  $stmt->bindParam(':bom_description', $bom_description);
  $stmt->execute();

  $new_id = $conn->lastInsertId();
  return $new_id;
}

function insertBomElements($conn, $new_id, $part_id, $amount){
  $stmt = $conn->prepare("INSERT INTO `bom_elements` (`bom_elements_id`, `bom_id_fk`, `part_id_fk`, `element_quantity`) VALUES (NULL, :bom_id, :part_id, :amount)");
  $stmt->bindParam(':bom_id', $new_id);
  $stmt->bindParam(':part_id', $part_id);
  $stmt->bindParam(':amount', $amount);
  $stmt->execute();
}

function getStockLevels($conn, $part_id){
  $stmt = $conn->query("SELECT location_name, stock_level_quantity FROM stock_levels JOIN location_names ON stock_levels.location_id_fk = location_names.location_id WHERE part_id_fk = $part_id");
  $stock = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $stock;
}

function getBomElements($conn, $bom_id){
  $stmt = $conn->query("SELECT part_name, element_quantity FROM bom_elements JOIN parts ON part_id_fk = parts.part_id WHERE bom_id_fk = $bom_id");
  $elements = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $elements;
}

function insertBackorderProducts($conn, $new_id, $product_id, $amount){
  $stmt = $conn->prepare("INSERT INTO `backorders_product_lookup` (`id`, `backorder_id`, `product_id`, `amount`) VALUES (NULL, :backorder_id, :product_id, :amount)");
  $stmt->bindParam(':backorder_id', $new_id);
  $stmt->bindParam(':product_id', $product_id);
  $stmt->bindParam(':amount', $amount);
  $stmt->execute();
}

function updateRow($conn, $part_id, $column, $table_name, $new_value){
  // bindParam is only for values, not for identifiers like table or column names
  $stmt = $conn->prepare("UPDATE " . $table_name . " SET " . $column . " = :new_value WHERE part_id = :part_id");
  $stmt->bindParam(':new_value', $new_value);
  $stmt->bindParam(':part_id', $part_id);
  $stmt->execute();
}