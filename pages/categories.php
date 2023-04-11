<?php
// Categories page
$basename = basename(__FILE__);
$title = 'Categories';
require_once('../includes/head.html');
include '../config/credentials.php';
include '../includes/SQL.php';
include '../includes/forms.php';
$table_name = "part_categories";

?>

<div class="container-fluid">
  <?php require_once('../includes/navbar.php'); ?>
  <br>
  <h4>Categories</h4>

  <?php
  $conn = connectToSQLDB($hostname, $username, $password, $database_name);

  // SQL query to retrieve category data
  $categories = getCategories($conn);

  echo "<pre>";
  print_r($categories);
  echo "</pre>";
  function generateTreeList($arr)
  {
    $url =
      $childNodes = array();
    foreach ($arr as $node) {
      $childNodes[$node['parent_category']][] = $node;
    }

    $treeList = '<ul id="category-tree">';
    foreach ($childNodes[1] as $node) {
      $treeList .= '<li><a href="' . $node['category_name'] . '">' . $node['category_name'] . '</a>';
      if (!empty($childNodes[$node['category_id']])) {
        $treeList .= generateChildTree($childNodes, $node['category_id']);
      }
      $treeList .= '</li>';
    }
    $treeList .= '</ul>';
    return $treeList;
  }

  function generateChildTree($childNodes, $parentId)
  {
    $childTree = '<ul>';
    foreach ($childNodes[$parentId] as $node) {
      $childTree .= '<li><a href="' . $node['category_name'] . '">' . $node['category_name'] . '</a>';
      if (!empty($childNodes[$node['category_id']])) {
        $childTree .= generateChildTree($childNodes, $node['category_id']);
      }
      $childTree .= '</li>';
    }
    $childTree .= '</ul>';
    return $childTree;
  }

  echo '<div id="jstree">';
  echo generateTreeList($categories);
  echo '</div>';

  ?>

  <script>
    // JSTree
    $('#jstree').jstree({
      "core": {
        "themes": {
          "theme": "database",
          "icons": false,
          "dots": true,
          "stripes": true,
          "ellipsis": true
        }
      },
      "plugins": ["themes", "html_data"]
    });

    // Add click event listeners to all parent nodes
    var parents = document.querySelectorAll("#category-tree li > ul");
    for (var i = 0; i < parents.length; i++) {
      parents[i].parentNode.classList.add("parent");
      parents[i].parentNode.addEventListener("click", toggleNode);
    }

    // Toggle the expanded state of a node
    function toggleNode(event) {
      var target = event.target || event.srcElement;
      if (target.classList.contains("parent")) {
        target.classList.toggle("expanded");
      }
    }
  </script>