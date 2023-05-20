<div class="container-fluid pb-3">
    <div>
        <table class="table table-sm table-responsive table-hover table-striped" style="font-size:12px"
            id="partStockHistoryTable"
            data-resizable="true"
            data-search="true"
            data-search-align="left"
            data-show-columns="true"
            data-reorderable-columns="true"
            data-cookie="true"
            data-cookie-id-table="PartsStockHistoryTableState">
            <thead>
                <tr>
                    @foreach ($nice_stockHistoryTableHeaders as $column_header)
                        <th data-field="{{ $column_header }}" {!! $column_header === 'Date' ? 'data-sortable="true"' : '' !!}>{{ $column_header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($stock_history as $row)
                    @php
                        $hist_id = $row['stock_lvl_chng_id'];
                    @endphp
                    <tr data-id="{{ $row['part_id_fk'] }}">
                        @foreach ($stockHistoryTableHeaders as $column_data)
                            <td data-editable="true" class="editable" data-id="{{ $hist_id }}" data-column="{{ $column_data }}">
                                {{ $column_data === 'stock_lvl_chng_comment' ? $row[$column_data] : $row[$column_data] }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>