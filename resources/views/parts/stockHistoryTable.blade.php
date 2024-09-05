<div class="container-fluid pb-3">
    <div>
        <table class="table table-sm table-responsive table-hover table-striped" style="font-size:12px"
            id="partStockHistoryTable" data-resizable="true" data-search="true" data-search-align="left"
            data-show-columns="true" {{-- data-reorderable-columns="true" --}} data-sort-name="Date" data-sort-order="desc" data-cookie="true"
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
                            <td data-editable="true" class="editable" data-id="{{ $hist_id }}"
                                data-column="{{ $column_data }}">
                                @if ($column_data === 'stock_lvl_chng_timestamp')
                                    {{-- Assuming it's a Carbon instance --}}
                                    {{ $row->$column_data->format('Y-m-d H:i:s') }}
                                @else
                                    {{ $row->$column_data }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
