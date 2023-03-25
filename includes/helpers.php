<?php
function validateSearchColumn($search_column, $column_names)
{
    // If the search column is not valid, default to the first column
    if (!in_array($search_column, $column_names)) {
        $search_column = $column_names[0];
        return $search_column;
    }
    else {
        return $search_column;
    }
}

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
        }
    }

    return $current_stock_level;
}