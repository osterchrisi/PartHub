<?php
// Including session but not the header - otherwise I have the header multiple times and BS Tables goes haywire
include 'session.php';
include '../config/credentials.php';
include 'SQL.php';
include 'forms.php';
include 'get.php';
include 'helpers.php';

$table_name = "parts";
dealWithCats();

$search_term = getSuperGlobal('search');
$search_category = getSuperGlobal('cat', ['all']);

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$column_names = getColumnNames($conn, $table_name);
$results_per_page = getSuperGlobal('resultspp', '50');

$categories = getCategories($conn);

include 'tables.php';
include 'pagination.php';
include '../config/inventory-columns.php';
$results_per_page = getSuperGlobal('resultspp', '50');

try {
    //* Essentially setting search column to everywhere because currently not in use in the search form
    $search_column = getSuperGlobal('search_column', 'everywhere');
    // The term from the search field
    $search_term = getSuperGlobal('search');
    //Get the number of results for pagination
    $total_rows = getTotalNumberOfRows($conn, $table_name, $search_column, $search_term, $column_names, $search_category, $user_id);

    if ($total_rows) {
      // Calculate the total number of pages for pagination
      $total_pages = ceil($total_rows / $results_per_page);
      $current_page = getCurrentPageNumber($total_pages);

      // Calculate the offset for the current page
      $offset = ($current_page - 1) * $results_per_page;

      $result = queryDB($table_name, $search_column, $search_term, $offset, $results_per_page, $conn, $column_names, $search_category, $user_id);

      buildPartsTable($result, $db_columns, $nice_columns, $total_stock, $conn, $table_name);
    }
    else {
      noResults();
    }
  } catch (Exception $e) {
    echo "<br>Error: " . $e->getMessage();
  }