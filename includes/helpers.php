<?php
// A few helper functions
function validateCurrentPage($current_page, $total_pages)
{
    // Validate the current page number
    if ($current_page < 1 || $current_page > $total_pages) {
        throw new Exception("Invalid page number, most likely no search results. That's an exception for now.");
    }
    else {
        return $current_page;
    }
}

// No results for queried table
function noResults()
{
    echo '<br><div class="alert alert-primary" role ="alert">';
    echo 'No results found :(';
    echo '</div>';
}

//Get current stock in location
function getCurrentStock($stock_levels, $location)
{

    foreach ($stock_levels as $entry) {
        if (isset($entry['location_id']) && $entry['location_id'] == $location) {
            $current_stock_level = $entry['stock_level_quantity'];
        } else {
            $current_stock_level = 0;
        }
    }

    return $current_stock_level;
}

// Sanitize user input string by removing leading/trailing white spaces and HTML special characters
function sanitizeString($input)
{
    $input = trim($input);
    $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return $input;
}

// Sanitize user input string by stripping out potentially dangerous characters
function sanitizeInput($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = filter_var($input, FILTER_SANITIZE_STRING);
    return $input;
}

// Check if a string is within a certain length range
function checkStringLength($input, $min, $max)
{
    $length = strlen($input);
    if ($length < $min || $length > $max) {
        return false;
    }
    return true;
}

// Validate an e-mail
function validateEmail($email)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'false';
    }
    return 'true';
}

/**
 * Converts the JSON encoded array from the selectized multiselect into a PHP array and puts it into the $_GET array
 * @return void
 */ 
function dealWithCats()
{
    $cat = $_GET['cat'][0]; // Get the JSON-encoded string from the $_GET arry

    $cat = json_decode($cat); // Convert the string to a PHP array

    // $cat is now an array of nested arrays, so we need to extract the category IDs
    $cat_ids = array();
    foreach ($cat as $cat_array) {
        $cat_ids[] = $cat_array[0];
    }

    // This is for the case when the array is empty (i.e. landing on the inventory page the first time)
    if (empty($cat_ids)) {
        $cat_ids[0] = 'all';
    }

    // And finally put it back where it came from!
    $_GET['cat'] = $cat_ids;
}

function createTempItemName($string, $l)
{
    $length = $l;
    $rand_id = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, $length);
    $temp_name = $string . $rand_id;
    return $temp_name;
}