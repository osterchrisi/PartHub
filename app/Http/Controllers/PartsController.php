<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\StockLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartsController extends Controller
{
    private static $table_name = 'parts';
    private static $id_field = 'part_id';
    private static $db_columns = array('state', 'part_name', 'part_description', 'part_comment', 'category_name', 'total_stock', 'part_footprint_fk', 'unit_name', "part_id");
    // 'state' doesn't actually exist but is there to make an empty column for boostrapTable's selected row to have a place
    private static $nice_columns = array('Name', 'Description', 'Comment', 'Category', 'Total Stock', 'Footprint', 'Unit', "ID");

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //* This is the alternate way for near future:
        //* $search_column = $request->get('search_column');

        $search_column = 'everywhere';
        $search_term = '';
        $column_names = Part::getColumnNames();
        $search_category = ['all'];
        $user_id = Auth::user()->id;

        $parts = Part::queryParts($search_column, $search_term, $column_names, $search_category, $user_id);

        // Calculate and append each part's total stock
        foreach ($parts as &$part) {
            $totalStock = self::calculateTotalStock($part['stock_levels']);
            $part['total_stock'] = $totalStock;
        }

        return view('parts.parts', [
            'title' => 'Parts',
            'parts' => $parts
        ]);
    }

    public function buildTable($parts, $width = "100%")
    {
        $db_columns = self::$db_columns;
        $nice_columns = self::$nice_columns;
        $table_name = self::$table_name;
        $id_field = self::$id_field;

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
            $part_id = $part['part_id'];
            echo "<tr data-id=" . $part['part_id'] . ">";

            foreach ($db_columns as $column_data) {
                if ($column_data == 'total_stock') {
                    $total_stock = $part['total_stock'];
                    echo "<td style='text-align:right' data-id=" . $part_id . " data-column=" . $column_data . " data-table_name=" . $table_name . " data-id_field=" . $id_field . ">" . $total_stock . "</td>";
                }
                // Category (editable category)
                elseif ($column_data == 'category_name') {
                    echo "<td data-editable='true' class='editable category' data-id=" . $part_id . " data-column=" . $column_data . " data-table_name=" . $table_name . " data-id_field=" . $id_field . ">" . $part['category'][$column_data] . "</td>";
                }
                elseif ($column_data == 'unit_name') {
                    echo "<td data-editable='true' class='editable category' data-id=" . $part_id . " data-column=" . $column_data . " data-table_name=" . $table_name . " data-id_field=" . $id_field . ">" . $part['unit'][$column_data] . "</td>";
                }
                // Select column (do nothing)
                elseif ($column_data == 'state') {
                    echo "<td></td>";
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
        // Fetch the part with its related stock levels
        $part = Part::with('stockLevels')->find($id)->toArray();

        // Calculate total stock level
        $total_stock = $this->calculateTotalStock($part['stock_levels']);

        // Return view
        return view('parts.showPart', ['part' => $part, 'total_stock' => $total_stock]);
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

    private function calculateTotalStock($stockLevels)
    {
        $total_stock = 0;

        foreach ($stockLevels as $stockLevel) {
            $total_stock += $stockLevel['stock_level_quantity'];
        }

        return $total_stock;
    }
}