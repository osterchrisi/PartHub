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
    // Make a list out of selected categories
    $cats_selected .= implode(", ", $search_category);

    // Also select all sub-categories of those categories
    $cats_resulting = $conn->prepare("WITH RECURSIVE selected_parents AS (
                                      SELECT category_id
                                      FROM part_categories
                                      WHERE category_id IN ($cats_selected)
                                    ),
                                    child_nodes AS (
                                      SELECT category_id
                                      FROM part_categories
                                      WHERE category_id IN (SELECT category_id FROM selected_parents)
                                      UNION ALL
                                      SELECT c.category_id
                                      FROM part_categories c
                                      INNER JOIN child_nodes cn ON c.parent_category = cn.category_id
                                    )
                                    SELECT * FROM child_nodes;");
    $cats_resulting->execute();
    $cats = $cats_resulting->fetchAll(PDO::FETCH_ASSOC);

    // Craft resulting category list for query
    $cats_queried = '(';
    $cats_queried .= implode(",", array_map(function ($category) {
      return $category['category_id'];
    }, $cats));
    $cats_queried .= ')';

    // Finally add it to the query statement
    $sql .= "AND part_category_fk IN " . $cats_queried;
  }

  // Filter for user
  $sql .= " AND part_owner_u_fk = :user_id";

  // Query
  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':search_term', "%$search_term%", PDO::PARAM_STR);
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
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
    // Make a list out of selected categories
    $cats_selected .= implode(", ", $search_category);

    // Also select all sub-categories of those categories
    $cats_resulting = $conn->prepare("WITH RECURSIVE selected_parents AS (
                                      SELECT category_id
                                      FROM part_categories
                                      WHERE category_id IN ($cats_selected)
                                    ),
                                    child_nodes AS (
                                      SELECT category_id
                                      FROM part_categories
                                      WHERE category_id IN (SELECT category_id FROM selected_parents)
                                      UNION ALL
                                      SELECT c.category_id
                                      FROM part_categories c
                                      INNER JOIN child_nodes cn ON c.parent_category = cn.category_id
                                    )
                                    SELECT * FROM child_nodes;");
    $cats_resulting->execute();
    $cats = $cats_resulting->fetchAll(PDO::FETCH_ASSOC);

    // Craft resulting category list for query
    $cats_queried = '(';
    $cats_queried .= implode(",", array_map(function ($category) {
      return $category['category_id'];
    }, $cats));
    $cats_queried .= ')';

    // Finally add it to the query statement
    $sql .= "AND part_category_fk IN " . $cats_queried;
  }

  // Filter for user
  $sql .= " AND part_owner_u_fk = :user_id";

  $sql .= " LIMIT :offset, :results_per_page";

  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':search_term', "%$search_term%", PDO::PARAM_STR);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  $stmt->bindValue(':results_per_page', $results_per_page, PDO::PARAM_INT);
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $stmt->execute();

  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $result;
}



function getTotalNumberOfBomRows($conn, $table_name, $search_term, $user_id)
{
  // Get the total number of rows in the table, filtered by the search term
  $sql = "SELECT bom_id, bom_name, bom_description FROM bom_names WHERE bom_name LIKE :search_term AND bom_owner_u_fk = :user_id";

  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':search_term', "%$search_term%", PDO::PARAM_STR);
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  $total_rows = $stmt->rowCount();
  return $total_rows;
}

function bom_query($conn, $table_name, $search_term, $offset, $results_per_page, $user_id)
{
  // Select a limited set of data from the table, based on the current page and number of results per page, filtered by the search term

  $sql = "SELECT bom_id, bom_name, bom_description FROM bom_names WHERE bom_name LIKE :search_term AND bom_owner_u_fk = :user_id LIMIT :offset, :results_per_page";
  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':search_term', "%$search_term%", PDO::PARAM_STR);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  $stmt->bindValue(':results_per_page', $results_per_page, PDO::PARAM_INT);
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $result;
}
/**
 * Get all parts owned by a specified user from the database.
 *
 * @param PDO $conn The database connection object.
 * @param int $user_id The ID of the user whose parts to retrieve.
 *
 * @return array An array of associative arrays representing the retrieved parts.
 */
function getAllParts($conn, $user_id)
{
  $stmt = $conn->prepare("SELECT part_id, part_name
                        FROM parts
                        WHERE part_owner_u_fk = :user_id");
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);       
  $stmt->execute();                 
  $all_parts = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $all_parts;
}

function getAllBoms($conn, $user_id)
{
  $stmt = $conn->query("SELECT bom_id, bom_name FROM bom_names WHERE bom_owner_u_id = :user_id");
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
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
  $sql = "SELECT bom_name, bom_description FROM bom_names WHERE bom_id = :bom_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':bom_id', $bom_id, PDO::PARAM_INT);
  $stmt->execute();
  $bom_name = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $bom_name;
}

function createBom($conn, $bom_name, $bom_description = NULL, $user_id)
{
  $stmt = $conn->prepare("INSERT INTO bom_names (
                            bom_id, bom_name, bom_description, bom_owner_g_fk, bom_owner_u_fk
                        ) VALUES (
                            NULL, :bom_name, :bom_description, NULL, :user_id
                        )");
  $stmt->bindParam(':bom_name', $bom_name, PDO::PARAM_STR);
  $stmt->bindParam(':bom_description', $bom_description, PDO::PARAM_STR);
  $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
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
  $stmt->bindParam(':bom_id', $new_id, PDO::PARAM_INT);
  $stmt->bindParam(':part_id', $part_id, PDO::PARAM_INT);
  $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
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
  $stmt = $conn->query("SELECT part_name, element_quantity, part_id, bom_elements_id
                        FROM bom_elements
                        JOIN parts ON part_id_fk = parts.part_id
                        WHERE bom_id_fk = $bom_id");
  $elements = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $elements;
}

/**
 * Update a row in the database table with a new value for a specified column.
 *
 * @param PDO $conn The database connection object.
 * @param int $id The ID of the row to update.
 * @param string $id_field The name of the ID column in the table, e.g. part_id, bom_id, ...
 * @param string $column The name of the column to update.
 * @param string $table_name The name of the table to update.
 * @param mixed $new_value The new value to set for the specified column.
 * 
 * @return void
 */
function updateRow($conn, $id, $id_field, $column, $table_name, $new_value)
{
  $stmt = $conn->prepare("UPDATE " . $table_name . " SET " . $column . " = :new_value WHERE " . $id_field . " = :id");
  $stmt->bindParam(':new_value', $new_value);
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
  $stmt->execute();
}

function getCategories($conn, $user_id)
{
  $sql = "SELECT *
          FROM part_categories
          WHERE part_category_owner_u_fk = :user_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
  $stmt->execute();
  $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $categories;
}

function getUserName($conn)
{
  $sql = "SELECT user_name FROM users WHERE user_id = :user_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
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
  $stmt->bindParam(':part_id', $part_id, PDO::PARAM_INT);
  $stmt->bindParam(':from_location', $from_location, PDO::PARAM_INT);
  $stmt->bindParam(':to_location', $to_location, PDO::PARAM_INT);
  $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
  $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
  $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
  $stmt->execute();

  $new_id = $conn->lastInsertId();
  return $new_id;
}

function getLocations($conn, $user_id)
{
  $stmt = $conn->prepare("SELECT *
                          FROM location_names
                          WHERE location_owner_u_fk = :user_id");
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
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
  $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
  $stmt->bindParam(':part_id', $part_id, PDO::PARAM_INT);
  $stmt->bindParam(':to_location', $to_location, PDO::PARAM_INT);
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
  $stmt->bindParam(':part_id', $part_id, PDO::PARAM_INT);
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
  $stmt->bindParam(':part_id', $part_id, PDO::PARAM_INT);
  $stmt->execute();
  $bom_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $bom_list;
}

function getPasswordHash($conn, $email)
{
  $stmt = $conn->prepare("SELECT user_id, user_passwd, user_name
                          FROM users
                          WHERE user_email = :email");
  $stmt->bindParam(':email', $email, PDO::PARAM_STR);
  $stmt->execute();
  $pw_hash = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $pw_hash;
}

function checkIfUserExists($conn, $email)
{
  $stmt = $conn->prepare("SELECT user_email
                          FROM users
                          WHERE user_email = :email");
  $stmt->bindParam(':email', $email, PDO::PARAM_STR);
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
  $stmt->bindParam(':user_name', $name, PDO::PARAM_STR);
  $stmt->bindParam(':user_email', $email, PDO::PARAM_STR);
  $stmt->bindParam(':user_passwd', $passwd, PDO::PARAM_STR);
  $stmt->execute();
  $new_id = $conn->lastInsertId();
  return $new_id;
}
/**
 * Insert new row into stock_levels table
 *
 * @param PDO $conn The PDO object for connecting to the database
 * @param int $part_id Part ID for which to create the stock level entry
 * @param int $to_location ID of location for which to create stock level entry
 * @param int $quantity Quantity of stock level entry
 * @return int ID of newly created stock level entry
 */
function stockEntry($conn, $part_id, $to_location, $quantity)
{
  $stmt = $conn->prepare("INSERT INTO stock_levels (
                              stock_level_id, part_id_fk, location_id_fk, stock_level_quantity
                          ) VALUES (
                              NULL, :part_id, :to_location, :quantity
                          )");
  $stmt->bindParam(':part_id', $part_id, PDO::PARAM_INT);
  $stmt->bindParam(':to_location', $to_location, PDO::PARAM_INT);
  $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
  $stmt->execute();

  $new_id = $conn->lastInsertId();
  return $new_id;
}

/**
 * Delete a row in a database table by its ID column name and ID
 *
 * @param PDO $conn The PDO object for connecting to the database
 * @param int $id The ID of the row to be deleted
 * @param string $table The name of the table
 * @param string $column The column name in which to find the ID
 * @return void
 */
function deleteRowById($conn, $id, $table, $column)
{
  $stmt = $conn->prepare("DELETE FROM " . $table . " WHERE " . $column . " = :id");
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
  $stmt->execute();
}