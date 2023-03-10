<?php

function getSearchColumn()
{
  // Get the column to search from the URL parameter "column"
  $search_column = isset($_GET['search_column']) ? trim($_GET['search_column']) : 'everywhere';
  //$search_column = validateSearchColumn($search_column, $column_names);
  return $search_column;
}

function getResultsPerPage()
{
  // Get the column to search from the URL parameter "column"
  $results_per_page = isset($_GET['resultspp']) ? trim($_GET['resultspp']) : '50';
  return $results_per_page;
}

function getSearchStatus()
{
  // Get the column to search from the URL parameter "column"
  $search_status = isset($_GET['search_status']) ? trim($_GET['search_status']) : 'all';
  return $search_status;
}


function getSearchTerm()
{
  // Get the termn to search for from the URL parameter "search"
  $search_term = isset($_POST['search']) ? $_POST['search'] : (isset($_GET['search']) ? $_GET['search'] : '');
  return $search_term;
}

function getCurrentPageNumber($total_pages)
{
  // Get the current page number from the URL parameter "page"
  $current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
  $current_page = validateCurrentPage($current_page, $total_pages);
  return $current_page;
}

function getPONumber()
{
  // Get the termn to search for from the URL parameter "search"
  $po_number = isset($_GET['po']) ? trim($_GET['po']) : '';
  ;
  return $po_number;
}

function getPartID()
{
  // Get the termn to search for from the URL parameter "search"
  $part_id = isset($_GET['part_id']) ? trim($_GET['part_id']) : '';
  ;
  return $part_id;
}

function getBomID()
{
  // Get the termn to search for from the URL parameter "search"
  $bom_id = isset($_GET['id']) ? trim($_GET['id']) : '';
  ;
  return $bom_id;
}

function getTotalStock($result)
{
  $total_stock = 0;
  foreach ($result as $row) {
    $s = $row['stock_level_quantity'];
    $total_stock += $s;
  }
  return $total_stock;
}