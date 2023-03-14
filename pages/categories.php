<?php
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

    // echo "<pre>";
    // print_r($categories);
    // echo "end ";
    function generateTreeList($arr) {
        $url = 
        $childNodes = array();
        foreach($arr as $node) {
            $childNodes[$node['parent_category']][] = $node;
        }
    
        $treeList = '<ul id="category-tree">';
        foreach($childNodes[1] as $node) {
            $treeList .= '<li><a href="'.$node['category_name'].'">'.$node['category_name'].'</a>';
            if(!empty($childNodes[$node['category_id']])) {
                $treeList .= generateChildTree($childNodes, $node['category_id']);
            }
            $treeList .= '</li>';
        }
        $treeList .= '</ul>';
        return $treeList;
    }
    
    function generateChildTree($childNodes, $parentId) {
        $childTree = '<ul>';
        foreach($childNodes[$parentId] as $node) {
            $childTree .= '<li><a href="'.$node['category_name'].'">'.$node['category_name'].'</a>';
            if(!empty($childNodes[$node['category_id']])) {
                $childTree .= generateChildTree($childNodes, $node['category_id']);
            }
            $childTree .= '</li>';
        }
        $childTree .= '</ul>';
        return $childTree;
    }
    
    echo generateTreeList($categories);

    ?>

    <style>
        /* Hide all child nodes by default */
#category-tree ul {
  display: none;
}

/* Show child nodes when the parent node is expanded */
#category-tree > li.expanded > ul {
  display: block;
}

/* Add expand/collapse icon to parent nodes */
#category-tree > li:before {
  content: "+";
  margin-right: 5px;
}

#category-tree > li.expanded:before {
  content: "-";
}

#category-tree li {
  list-style-type: none;
}
</style>

<script>
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