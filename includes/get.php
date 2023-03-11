<?php

function getSuperGlobal($sg, $fb = ""){
  // Get a $_GET variable $sg or set it to $fb
  return isset($_GET[$sg]) ? $_GET[$sg] : $fb;
}


function getCurrentPageNumber($total_pages)
{
  // Get the current page number from the URL parameter "page"
  $current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
  $current_page = validateCurrentPage($current_page, $total_pages);
  return $current_page;
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