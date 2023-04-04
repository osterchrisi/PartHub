<?php
function connectToSQLDB($hostname, $username, $password, $database_name)
{
  // Connect to the database using PDO
  $conn = new PDO("mysql:host=$hostname;dbname=$database_name", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $conn;
}

function getColumnNames($conn, $table_name)
{
  // Select the column names of the table for the dropdown menu 
  $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?";
  $stmt = $conn->prepare($sql);
  $stmt->execute([$table_name]);
  $column_names = $stmt->fetchAll(PDO::FETCH_COLUMN);
  return $column_names;
}
function getTotalNumberOfRows($conn, $table_name, $search_column, $search_term, $column_names, $search_category, $user_id)
{
  // Get the total number of rows in the table, filtered by the search term
  $sql = "SELECT COUNT(*) as total FROM $table_name WHERE ";

  // Seach Column(s)
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
  }
  else {
    // Search only the specified column
    $sql .= "$search_column LIKE :search_term ";
  }

  // Seach Category(s)
  if (in_array('all', $search_category)) {
    $sql .= "AND part_category_fk > 0";
  }
  else {
    $string = '(';
    $string .= implode(", ", $search_category);
    $string .= ")";

    $sql .= "AND part_category_fk IN " . $string;
  }
  $sql .= " AND part_owner_u_fk = :user_id";

  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':search_term', "%$search_term%", PDO::PARAM_STR);
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $total_rows = $row['total'];
  return $total_rows;
}

function queryDB($table_name, $search_column, $search_term, $offset, $results_per_page, $conn, $column_names, $search_category, $user_id)
{
  // Select a limited set of data from the table, based on the current page and number of results per page, filtered by the search term and search column
  $sql = "SELECT *, part_id as 'id' FROM $table_name 
          JOIN part_categories ON parts.part_category_fk = part_categories.category_id
          JOIN part_units ON parts.part_unit_fk = part_units.unit_id
          WHERE ";

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
  }
  else {
    // Search only the specified column
    $sql .= "$search_column LIKE :search_term ";
  }

  // Seach Category(s)
  if (in_array('all', $search_category)) {
    $sql .= "AND part_category_fk > 0 ";
  }
  else {
    $string = '(';
    $string .= implode(", ", $search_category);
    $string .= ") ";

    $sql .= "AND part_category_fk IN " . $string;
  }

  $sql .= " AND part_owner_u_fk = :user_id";

  $sql .= " LIMIT :offset, :results_per_page";

  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':search_term', "%$search_term%", PDO::PARAM_STR);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  $stmt->bindValue(':results_per_page', $results_per_page, PDO::PARAM_INT);
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
  $stmt->execute();

  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $result;
}

function backorder_query($table_name, $search_column, $search_term, $offset, $results_per_page, $conn, $column_names, $search_status)
{
  // Select a limited set of data from the table, based on the current page and number of results per page, filtered by the search term

  $sql = "SELECT customer_name, customer_po, created_at, product_name, amount, status_name FROM backorders JOIN customers ON backorders.customer_id = customers.id JOIN backorders_product_lookup ON backorders.id = backorders_product_lookup.backorder_id JOIN products ON backorders_product_lookup.product_id = products.id JOIN backorder_statuses ON backorders.backorder_status = backorder_statuses.id WHERE ";

  if ($search_column == 'everywhere') {
    // Search all columns
    $sql .= "CONCAT(customer_name, customer_po, created_at, product_name, status_name) LIKE :search_term ";
  }
  else {
    // Search only the specified column
    $sql .= "$search_column LIKE :search_term ";
  }

  $sql .= "AND backorder_status LIKE ";
  if ($search_status == 1) {
    $sql .= "1 ";
  }
  elseif ($search_status == 2) {
    $sql .= "2 ";
  }
  elseif ($search_status == 3) {
    $sql .= "3 ";
  }
  elseif ($search_status == 'all') {
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

function show_backorder_query($table_name, $search_column, $search_term, $offset, $results_per_page, $conn, $column_names, $search_status)
{
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

function getTotalNumberOfBackorderRows($conn, $table_name, $search_column, $search_term, $search_status)
{
  // Get the total number of rows in the table, filtered by the search term
  $sql = "SELECT customer_name, customer_po, created_at, product_name, amount, backorder_status FROM backorders JOIN customers ON backorders.customer_id = customers.id JOIN backorders_product_lookup ON backorders.id = backorders_product_lookup.backorder_id JOIN products ON backorders_product_lookup.product_id = products.id JOIN backorder_statuses ON backorders.backorder_status = backorder_statuses.id WHERE ";

  if ($search_column == 'everywhere') {
    // Search all columns
    $sql .= "CONCAT(customer_name, customer_po, created_at, product_name, backorder_status) LIKE :search_term ";
  }
  else {
    // Search only the specified column
    $sql .= "$search_column LIKE :search_term ";
  }

  $sql .= "AND backorder_status LIKE ";
  if ($search_status == 1) {
    $sql .= "1 ";
  }
  elseif ($search_status == 2) {
    $sql .= "2 ";
  }
  elseif ($search_status == 3) {
    $sql .= "3 ";
  }
  elseif ($search_status == 'all') {
    $sql .= "'%' ";
  }

  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':search_term', "%$search_term%", PDO::PARAM_STR);
  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  $total_rows = $stmt->rowCount();
  return $total_rows;
}

function getTotalNumberOfBomRows($conn, $table_name, $search_term)
{
  // Get the total number of rows in the table, filtered by the search term
  $sql = "SELECT bom_id, bom_name, bom_description FROM bom_names WHERE bom_name LIKE :search_term";

  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':search_term', "%$search_term%", PDO::PARAM_STR);
  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  $total_rows = $stmt->rowCount();
  return $total_rows;
}

function bom_query($conn, $table_name, $search_term, $offset, $results_per_page)
{
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

function getBackordersCustomers($conn)
{
  $stmt = $conn->query("SELECT id, customer_name FROM customers");
  $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $customers;
}

function getBackordersProducts($conn)
{
  $stmt = $conn->query("SELECT id, product_name FROM products");
  $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $products;
}

function getAllParts($conn)
{
  $stmt = $conn->query("SELECT part_id, part_name FROM parts");
  $all_parts = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $all_parts;
}

function getAllBoms($conn)
{
  $stmt = $conn->query("SELECT bom_id, bom_name FROM bom_names");
  $boms = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $boms;
}

function getPartName($conn, $part_id)
{
  $sql = "SELECT part_name FROM parts WHERE part_id = :part_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':part_id', $part_id, PDO::PARAM_INT);
  $stmt->execute();
  $part_name = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $part_name;
}

function getBomName($conn, $bom_id)
{
  $sql = "SELECT bom_name FROM bom_names WHERE bom_id = :bom_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':bom_id', $bom_id, PDO::PARAM_INT);
  $stmt->execute();
  $bom_name = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $bom_name;
}
function createBackorder($conn, $customer_id, $customer_po)
{
  $stmt = $conn->prepare("INSERT INTO backorders (id, customer_id, customer_po, created_at) VALUES (NULL, :customer_id, :customer_po, current_timestamp())");
  $stmt->bindParam(':customer_id', $customer_id);
  $stmt->bindParam(':customer_po', $customer_po);
  $stmt->execute();

  $new_id = $conn->lastInsertId();
  return $new_id;
}

function createBom($conn, $bom_name, $bom_description = NULL)
{
  $stmt = $conn->prepare("INSERT INTO bom_names (
                            bom_id, bom_name, bom_description
                        ) VALUES (
                            NULL, :bom_name, :bom_description
                        )");
  $stmt->bindParam(':bom_name', $bom_name);
  $stmt->bindParam(':bom_description', $bom_description);
  $stmt->execute();

  $new_id = $conn->lastInsertId();
  return $new_id;
}

function insertBomElements($conn, $new_id, $part_id, $amount)
{
  $stmt = $conn->prepare("INSERT INTO bom_elements (
                            bom_elements_id, bom_id_fk, part_id_fk, element_quantity
                        ) VALUES (
                            NULL, :bom_id, :part_id, :amount)");
  $stmt->bindParam(':bom_id', $new_id);
  $stmt->bindParam(':part_id', $part_id);
  $stmt->bindParam(':amount', $amount);
  $stmt->execute();
}

function getStockLevels($conn, $part_id)
{
  $stmt = $conn->query("SELECT location_id, location_name, stock_level_quantity
                        FROM stock_levels
                        JOIN location_names ON stock_levels.location_id_fk = location_names.location_id 
                        WHERE part_id_fk = $part_id 
                        AND stock_level_quantity > 0");
  $stock = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $stock;
}

function getBomElements($conn, $bom_id)
{
  $stmt = $conn->query("SELECT part_name, element_quantity, part_id
                        FROM bom_elements
                        JOIN parts ON part_id_fk = parts.part_id
                        WHERE bom_id_fk = $bom_id");
  $elements = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $elements;
}

function insertBackorderProducts($conn, $new_id, $product_id, $amount)
{
  $stmt = $conn->prepare("INSERT INTO backorders_product_lookup (id, backorder_id, product_id, amount) VALUES (NULL, :backorder_id, :product_id, :amount)");
  $stmt->bindParam(':backorder_id', $new_id);
  $stmt->bindParam(':product_id', $product_id);
  $stmt->bindParam(':amount', $amount);
  $stmt->execute();
}

function updateRow($conn, $part_id, $column, $table_name, $new_value)
{
  // bindParam is only for values, not for identifiers like table or column names
  $stmt = $conn->prepare("UPDATE " . $table_name . " SET " . $column . " = :new_value WHERE part_id = :part_id");
  $stmt->bindParam(':new_value', $new_value);
  $stmt->bindParam(':part_id', $part_id);
  $stmt->execute();
}

function getCategories($conn)
{
  $sql = "SELECT * FROM part_categories";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $categories;
}

function getUserName($conn)
{
  $sql = "SELECT user_name FROM users WHERE user_id = :user_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':user_id', $_SESSION['user_id']);
  $stmt->execute();
  $name = $stmt->fetchAll(PDO::FETCH_ASSOC);
  // Need to extract the user name because $name is an array
  $name = $name[0]['user_name'];
  return $name;
}

function stockChange($conn, $part_id, $from_location, $to_location, $quantity, $comment, $user_id)
{
  $stmt = $conn->prepare("INSERT INTO stock_level_change_history (
                            stock_lvl_chng_id, 
                            part_id_fk, 
                            from_location_fk, 
                            to_location_fk, 
                            stock_lvl_chng_quantity, 
                            stock_lvl_chng_timestamp, 
                            stock_lvl_chng_comment, 
                            stock_lvl_chng_user_fk
                        ) VALUES (
                            NULL, 
                            :part_id, 
                            :from_location, 
                            :to_location, 
                            :quantity, 
                            CURRENT_TIMESTAMP, 
                            :comment, 
                            :user_id
)");
  $stmt->bindParam(':part_id', $part_id);
  $stmt->bindParam(':from_location', $from_location);
  $stmt->bindParam(':to_location', $to_location);
  $stmt->bindParam(':quantity', $quantity);
  $stmt->bindParam(':comment', $comment);
  $stmt->bindParam(':user_id', $user_id);
  $stmt->execute();

  $new_id = $conn->lastInsertId();
  return $new_id;
}

function getLocations($conn)
{
  $stmt = $conn->prepare("SELECT *
                          FROM location_names");
  $stmt->execute();
  $loc = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $loc;
}

/**
 * Inserts or updates a row into the stock_levels table
 *
 * @param PDO $conn The PDO object for connecting to the database
 * @param int $part_id ID for which to insert or update row
 * @param int $quantity The quantity of the part
 * @param string $to_location The location in which the part resides
 * @return int The ID of the inserted or updated row
 */
function changeQuantity($conn, $part_id, $quantity, $to_location)
{
  $stmt = $conn->prepare("INSERT INTO stock_levels
                            (part_id_fk, location_id_fk, stock_level_quantity)
                          VALUES
                            (:part_id, :to_location, :quantity)
                          ON DUPLICATE KEY
                            UPDATE stock_level_quantity = :quantity");
  $stmt->bindParam(':quantity', $quantity);
  $stmt->bindParam(':part_id', $part_id);
  $stmt->bindParam(':to_location', $to_location);
  $stmt->execute();
  $new_id = $conn->lastInsertId();
  return $new_id;
}

function getPartStockHistory($conn, $part_id)
{
  $stmt = $conn->prepare("SELECT *,
                                  from_loc.location_name AS from_location_name,
                                  to_loc.location_name AS to_location_name 
                          FROM stock_level_change_history
                          LEFT JOIN location_names from_loc ON from_location_fk = from_loc.location_id
                          LEFT JOIN location_names to_loc ON to_location_fk = to_loc.location_id
                          JOIN users ON stock_lvl_chng_user_fk = user_id
                          WHERE part_id_fk = :part_id");
  $stmt->bindParam(':part_id', $part_id);
  $stmt->execute();
  $hist = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $hist;
}

function getPartInBoms($conn, $part_id)
{
  $stmt = $conn->prepare("SELECT * 
                          FROM bom_elements
                          JOIN bom_names ON bom_id_fk = bom_id
                          WHERE part_id_fk = :part_id");
  $stmt->bindParam(':part_id', $part_id);
  $stmt->execute();
  $bom_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $bom_list;
}

function getPasswordHash($conn, $email)
{
  $stmt = $conn->prepare("SELECT user_id, user_passwd, user_name
                          FROM users
                          WHERE user_email = :email");
  $stmt->bindParam(':email', $email);
  $stmt->execute();
  $pw_hash = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $pw_hash;
}

function checkIfUserExists($conn, $email)
{
  $stmt = $conn->prepare("SELECT user_email
                          FROM users
                          WHERE user_email = :email");
  $stmt->bindParam(':email', $email);
  $stmt->execute();
  return $stmt->rowCount();
}

function createUser($conn, $email, $passwd, $name)
{
  $stmt = $conn->prepare("INSERT INTO users (
                            user_name,
                            user_email,
                            user_passwd,
                            register_date,
                            last_login
                        ) VALUES (
                            :user_name,
                            :user_email,
                            :user_passwd,
                            CURRENT_TIMESTAMP,
                            CURRENT_TIMESTAMP
                        )");
  $stmt->bindParam(':user_name', $name);
  $stmt->bindParam(':user_email', $email);
  $stmt->bindParam(':user_passwd', $passwd);
  $stmt->execute();
  $new_id = $conn->lastInsertId();
  return $new_id;
}

function stockEntry($conn, $part_id, $to_location, $quantity)
{
  $stmt = $conn->prepare("INSERT INTO stock_levels (
                              stock_level_id, part_id_fk, location_id_fk, stock_level_quantity
                          ) VALUES (
                              NULL, :part_id, :to_location, :quantity
                          )");
  $stmt->bindParam(':part_id', $part_id);
  $stmt->bindParam(':to_location', $to_location);
  $stmt->bindParam(':quantity', $quantity);
  $stmt->execute();

  $new_id = $conn->lastInsertId();
  return $new_id;
}