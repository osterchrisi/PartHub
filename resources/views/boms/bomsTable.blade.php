<div>
    <table class="table table-sm table-responsive table-hover table-striped" style="font-size:12px" id="bom_list_table"
        data-resizable="true" data-pagination="true" data-show-columns="true" data-reorderable-columns="true"
        data-cookie="true" data-cookie-id-table="BomListTableState" data-cookie-storage="localStorage"
        data-max-moving-rows="100" data-multiple-select-row="true" data-click-to-select="true">
        <thead>
            <tr>
                <th data-field="state" data-checkbox="true"></th>
                @foreach ($nice_columns as $column_header)
                    <th data-field="{{ $column_header }}">{{ $column_header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($bom_list as $row)
                @php
                    $bom_id = $row['bom_id'];
                @endphp
                <tr data-id="{{ $row['bom_id'] }}">
                    @foreach ($db_columns as $column_data)
                        @if ($column_data != 'state')
                            <td data-editable="true" class="editable" data-id="{{ $bom_id }}"
                                data-column="{{ $column_data }}" data-table_name="{{ $table_name }}"
                                data-id_field="{{ $id_field }}">{{ $row[$column_data] }}</td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
