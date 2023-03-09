<?php

namespace OnlineSid\DataTabulator;

class DataTabulator
{
    /**
     * @var array
     */
    private $rows;

    /**
     * DataTabulator constructor.
     * @param array $rows
     */
    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    /**
     * See the diagram below:
     *
     * $column_label        |  $column_name ($column_id)
     * ---------------------+-------------------------
     * $row_name ($row_id)  |  $aggregate
     *
     * @param string $column_label
     * @param string $row_id
     * @param string $row_name
     * @param string $column_id
     * @param string $column_name
     * @param string $aggregate
     * @return Table
     */
    public function to2DTable($column_label, $row_id, $row_name, $column_id, $column_name, $aggregate)
    {
        $table_data = [];
        $rows = $this->rows;
        $key_separator = '___';
        $tmp = [];
        $unique_rows = [];
        $unique_columns = [];
        foreach ($rows as $row)
        {
            $row_id_value = $row[$row_id];
            $row_name_value = $row[$row_name];

            $column_id_value = $row[$column_id];
            $column_name_value = $row[$column_name];

            $key = $row_id_value.$key_separator.$column_id_value;
            if (!isset($tmp[$key])) {
                $tmp[$key] = 0;
            }

            if (!isset($unique_rows[$row_id_value])) {
                $unique_rows[$row_id_value] = [
                    'id' => $row_id_value,
                    'name' => $row_name_value,
                ];
            }

            if (!isset($unique_columns[$column_id_value])) {
                $unique_columns[$column_id_value] = [
                    'id' => $column_id_value,
                    'name' => $column_name_value,
                ];
            }

            $tmp[$key] += $row[$aggregate];
        }

        $table_data = [];

        // headers
        $row_headers = [$column_label,];
        foreach ($unique_columns as $u_col) {
            $row_headers[] = $u_col['id'];
        }
        $table_data[] = $row_headers;

        foreach ($unique_rows as $u_row) {
            $arr = [$u_row['id']];
            foreach ($unique_columns as $u_col) {
                $key = $u_row['id'].$key_separator.$u_col['id'];
                if (isset($tmp[$key])) {
                    $arr[] = $tmp[$key];
                } else {
                    $arr[] = 0;
                }
            }
            $table_data[] = $arr;
        }

        return new Table($table_data, $unique_rows, $unique_columns);
    }
}