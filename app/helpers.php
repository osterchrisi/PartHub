<?php
function buildHTMLTable($column_names, $nice_columns, $table_data, $width = "100%")
{
    $html = "<div style='overflow-x:auto;'>";
    $html .= "<table class='table table-striped table-hover table-sm' style='width: " . $width . "; font-size:12px'>";

    // Table headers
    $html .= "<thead>";
    $html .= "<tr>";
    foreach ($nice_columns as $column_header) {
        $html .= "<th>$column_header</th>";
    }
    $html .= "</tr>";
    $html .= "</thead>";

    // Table rows
    foreach ($table_data as $row) {
        $html .= "<tr>";
        foreach ($column_names as $column_data) {
            $html .= "<td>" . $row[$column_data] . "</td>";
        }
        $html .= "</tr>";
    }
    $html .= "</table>";
    $html .= "</div>";
    return $html;
}