<?php
/**
 *@file This is essentially a copy of the code from inventory.php for the purpose of updating the parts table after adding or deleting parts 
 */

// Including session but not the header - otherwise I have the header multiple times and BS Tables goes haywire
include 'session.php';
include '../config/credentials.php';
include 'SQL.php';
include 'forms.php';
include 'get.php';
include 'helpers.php';

$table_name = "bom_names";
$results_per_page = getSuperGlobal('resultspp', '50');

$conn = connectToSQLDB($hostname, $username, $password, $database_name);

include 'tables.php';
include 'pagination.php';
include '../config/bom-list-columns.php';
$results_per_page = getSuperGlobal('resultspp', '50');

try {
  $search_term = getSuperGlobal('search');
  $total_rows = getTotalNumberOfBomRows($conn, $table_name, $search_term, $user_id);

    if ($total_rows) {
      // Calculate the total number of pages for pagination
      $total_pages = ceil($total_rows / $results_per_page);
      $current_page = getCurrentPageNumber($total_pages);

      // Calculate the offset for the current page
      $offset = ($current_page - 1) * $results_per_page;

      $bom_list = bom_query($conn, $table_name, $search_term, $offset, $results_per_page, $user_id);

      buildBomListTable($bom_list, $db_columns, $nice_columns);
    }
    else {
      noResults();
    }
  } catch (Exception $e) {
    echo "<br>Error: " . $e->getMessage();
  }