@php
use App\Models\StockLevel;
use App\Http\Controllers\PartsController;
@endphp

<div>
    <table class="table table-sm table-responsive table-hover table-striped"
           style="font-size:12px"
           id="BomDetailsTable"
           data-resizable="true"
           data-search="true"
           data-search-align="left"
           data-show-columns="true"
           data-reorderable-columns="true"
           data-cookie="true"
           data-cookie-id-table="BomDetailsTableState"
           data-max-moving-rows="100">
        <thead>
            <tr>
                @foreach ($nice_columns as $column_header)
                    @if ($column_header == 'Quantity needed' || $column_header == 'Total stock available')
                        <th data-halign="right" data-field="{{ $column_header }}">{{ $column_header }}</th>
                    @elseif ($column_header == 'Can build')
                        <th data-halign="right" data-field="{{ $column_header }}" data-sortable="true">{{ $column_header }}</th>
                    @else
                        <th data-field="{{ $column_header }}">{{ $column_header }}</th>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($bom_elements as $row)
                @php
                    $part_id = $row->part_id;
                    $part_name = $row->part_name;
                    $bom_elements_id = $row->bom_elements_id;
                    $stock = StockLevel::getStockLevelsByPartID($part_id); //getStockLevels($conn, $part_id);
                    $total_stock = new PartsController();//getTotalStock($stock);
                    $total_stock = $total_stock->calculateTotalStock($stock);
                @endphp
                <tr data-id="{{ $row->part_id }}">
                    @foreach ($db_columns as $column_data)
                        @if ($column_data == 'stock_available')
                            <td style="text-align:right">
                                <a tabindex="0" role="button" data-bs-trigger="focus" data-bs-toggle="popover"
                                   data-bs-title="Stock for {{ $part_name }}" data-bs-html="true"
                                   data-bs-content="{{ buildHTMLTable(['location_name', 'stock_level_quantity'], ['Location', 'Quantity'], StockLevel::getStockLevelsByPartID($part_id)) }}"
                                   data-bs-sanitize="false" href="#">{{ $total_stock }}</a>
                            </td>
                        @elseif ($column_data == 'element_quantity')
                            <td style="text-align:right" class="editable"
                                data-id="{{ $bom_elements_id }}" data-column="{{ $column_data }}"
                                data-table_name="bom_elements" data-id_field="bom_elements_id">
                                {{ $row->$column_data }}
                            </td>
                        @elseif ($column_data == 'can_build')
                            @php
                                $can_build = floor($total_stock / $row['element_quantity']);
                            @endphp
                            <td style="text-align:right" data-id="{{ $part_id }}" data-column="{{ $column_data }}">{{ $can_build }}</td>
                        @else
                            <td data-id="{{ $part_id }}" data-column="{{ $column_data }}">{{ $row->$column_data }}</td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
