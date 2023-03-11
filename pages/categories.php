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

echo "<pre>";
var_dump($categories);
echo "</pre>";

// PHP code to generate category tree
// function buildCategoryTree($categories, $parentId = 0) {
// $tree = array();
// foreach ($categories as $category) {
// if ($category['parent_id'] == $parentId) {
// $children = buildCategoryTree($categories, $category['id']);
// if ($children) {
// $category['children'] = $children;
// }
// $tree[] = $category;
// }
// }
// return $tree;
// }

// Generate category tree
$categoryTree = buildCategoryTree($categories);

// Convert PHP array to JSON format
$categoryTreeJson = json_encode($categoryTree);
?>

<script>
var categoryTree = JSON.parse(
<?php echo $categoryTreeJson; ?>);
</script>