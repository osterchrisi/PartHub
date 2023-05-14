<?php

namespace App\Http\Controllers;

use App\Models\Part;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search_column = 'everywhere';
        $search_term = '';
        $column_names = Part::getColumnNames();
        $search_category = ['all'];
        $user_id = Auth::user()->id;

        $parts = Part::queryParts($search_column, $search_term, $column_names, $search_category, $user_id);

        return view('parts', [
            'title' => 'Parts',
            'parts' => $parts
        ]);
    }

    public function buildTable($parts, $db_columns, $nice_columns, $total_stock, $conn, $table_name, $id_field, $width = "100%")
    {
        echo '<div>';
        echo '<table
        class="table table-sm table-responsive table-hover table-striped"
        style="font-size:12px"
        id="parts_table"
        data-resizable="true"
        data-search="true"
        data-search-time-out=""
        data-search-selector="#filter"
        data-search-align="left"
        data-pagination="true"
        data-show-columns="true"
        data-reorderable-columns="true"
        data-cookie="true"
        data-cookie-id-table="PartsTableState"
        data-cookie-storage="localStorage"
        data-max-moving-rows="100"
        data-multiple-select-row="true"
        data-click-to-select="true"
        >';

        // Table headers
        echo "<thead>";
        echo "<tr>";
        // This column is for Bootstrap Table Click-To-Select to work
        echo '<th data-field="state" data-checkbox="true"></th>';
        foreach ($nice_columns as $column_header) {
            if ($column_header == 'Total Stock') {
                // Removing the in-table links to stock levels again...
                // echo "<th data-sortable='true' data-sorter='NumberURLSorter' data-field='$column_header'>$column_header</th>";
                echo "<th data-sortable='true' data-field='$column_header'>$column_header</th>";
            }
            else {
                echo "<th data-sortable='true' data-field='$column_header'>$column_header</th>";
            }
        }
        echo "</tr>";
        echo "</thead>";

        echo "<tbody>";
        // Table rows
        foreach ($parts as $part) {
            // echo "<tr>";
            $part_id = $part['part_id'];
            echo "<tr data-id=" . $part['part_id'] . ">";
            foreach ($db_columns as $column_data) {
                // Removing the in-table links to stock levels again...
                if ($column_data == 'total_stock') {
                    // Get total stock
                    $stock = getStockLevels($conn, $part_id);
                    $total_stock = getTotalStock($stock);
                    // Display total stock number as link to showing stock levels
                    // echo "<td style='text-align:right' data-id=" . $part_id . " data-column=" . $column_data . " data-table_name=" . $table_name . "><a href='show-stock.php?part_id=$part_id'>" . $total_stock . "</a></td>";
                    echo "<td style='text-align:right' data-id=" . $part_id . " data-column=" . $column_data . " data-table_name=" . $table_name . " data-id_field=" . $id_field . ">" . $total_stock . "</td>";
                }
                // Category (editable category)
                elseif ($column_data == 'category_name') {
                    echo "<td data-editable='true' class='editable category' data-id=" . $part_id . " data-column=" . $column_data . " data-table_name=" . $table_name . " data-id_field=" . $id_field . ">" . $part[$column_data] . "</td>";
                }
                // Select column (do nothing)
                elseif ($column_data == 'state') {
                    ;
                }
                // Part ID (non-editable)
                elseif ($column_data == 'part_id') {
                    echo "<td data-id=" . $part_id . " data-column=" . $column_data . " data-table_name=" . $table_name . " data-id_field=" . $id_field . ">" . $part[$column_data] . "</td>";
                }
                // Any other table data available
                else {
                    echo "<td data-editable='true' class='editable' data-id=" . $part_id . " data-column=" . $column_data . " data-table_name=" . $table_name . " data-id_field=" . $id_field . ">" . $part[$column_data] . "</td>";
                }
            }

            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}