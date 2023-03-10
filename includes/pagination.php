<?php
function displayPaginationLinks($total_pages, $current_page){
  // Display the pagination links

  $q = $_SERVER['QUERY_STRING']; // query string including page number
  $s = $_SERVER['PHP_SELF']; // URL without http://localhost/ and without query string
    
  echo "<nav>";
  echo "<ul class = 'pagination'>";

  if (isset($_GET['page'])){ // page=X is present in URL
    // Remove the page=X part from the URL
    $r = preg_replace("/&page=.+/", "", $q); // remove the page parameter from the URL
    
    for ($i = 1; $i <= $total_pages; $i++) {
      if ($i == ($_GET['page'])){ //Must be current page
        echo "<li class = 'page-item active'>";
        echo "<a class = 'page-link' href='$s?$r&page=$i'>$i</a>";
        echo "</li>";
      }
      else {
        echo "<li class = 'page-item'>";
        echo "<a class = 'page-link' href='$s?$r&page=$i'>$i</a>";
        echo "</li>";
      }
    }
  }
  else { // page=X is not yet present in URl
    for ($i = 1; $i <= $total_pages; $i++) {
      echo "<li class = 'page-item'>";
      if ($i == 1){ // Must be first page
        echo "<li class = 'page-item active'>";
        echo "<a class = 'page-link' href='$s?$q&page=$i'>$i</a>";
        echo "</li>";
      }
      else {
        echo "<li class = 'page-item'>";
        echo "<a class = 'page-link' href='$s?$q&page=$i'>$i</a>";
        echo "</li>";
      }    
    }
  }
  echo "</ul>";
  echo "</nav>";   



//   echo "<div class='pagination'><br><br>Go to page ";
//   for ($i = 1; $i <= $total_pages; $i++) {
//     //   echo "<a href='?page=$i'>$i</a> ";
//       echo "<a href='$s?$q&page=$i'>$i</a> ";
//   }
//   echo "</div>";    
}   