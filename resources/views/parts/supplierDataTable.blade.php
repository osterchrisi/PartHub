@dump($supplierData)
<div class="mt-3" style="overflow-x:auto;">
    <table class="table table-striped table-hover table-sm" style="font-size:12px" id="partSupplierDataTable">
        <thead>
            <tr>
                @foreach ($nice_supplierDataTableHeaders as $column_header)
                    <th>{{ $column_header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($supplierData as $row)
                <tr>
                    @foreach ($supplierDataTableHeaders as $column_data)
                        @if ($column_data === 'supplier_id_fk')
                            <td data-id="{{ $row->id }}" data-column="{{ $column_data }}"
                                data-table_name="{{ $supplierDataTableName }}"
                                data-id_field="{{ $supplierDataTableIdField }}"
                                data-editable="true"
                                class="editable supplier">
                                {{ $row->supplier->supplier_name }}</td>
                        @else
                            <td data-id="{{ $row->id }}" data-column="{{ $column_data }}"
                                data-table_name="{{ $supplierDataTableName }}"
                                data-id_field="{{ $supplierDataTableIdField }}">{{ $row->$column_data }}</td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
