<?php

use Carbon\Carbon;

function buildHTMLTable($column_names, $nice_columns, $table_data, $width = '100%')
{
    $html = "<div style='overflow-x:auto;'>";
    $html .= "<table class='table table-striped table-hover table-sm' style='width: " . e($width) . "; font-size:12px'>";

    // Table headers
    $html .= '<thead><tr>';
    foreach ($nice_columns as $column_header) {
        $html .= '<th>' . e($column_header) . '</th>';
    }
    $html .= '</tr></thead>';

    // Table rows
    $html .= '<tbody>';
    foreach ($table_data as $row) {
        $html .= '<tr>';
        foreach ($column_names as $column_data) {
            $val = $row[$column_data] ?? '';
            // Cast to string so numbers/nulls behave consistently
            $html .= '<td>' . e((string) $val) . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</tbody></table></div>';

    return $html;
}

function calculateTotalStock($stockLevels)
{
    $total_stock = 0;

    foreach ($stockLevels as $stockLevel) {
        $total_stock += $stockLevel['stock_level_quantity'];
    }

    return $total_stock;
}

if (!function_exists('convertToUserTimezone')) {
    function convertToUserTimezone($timestamp, $timezone = null)
    {
        if (!$timestamp) {
            return null;
        }

        $timezone = $timezone ?? config('app.timezone');

        return Carbon::parse($timestamp)->setTimezone($timezone);
    }
}
