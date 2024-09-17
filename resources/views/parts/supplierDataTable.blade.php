{{-- @dump($supplierData) --}}
<div class="mt-3">
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
                            <td data-editable="true" class="editable supplierData" data-id="{{ $row->id }}"
                                data-column="{{ $column_data }}" data-table_name="{{ $supplierDataTableName }}"
                                data-id_field="{{ $supplierDataTableIdField }}">

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
    <button type="button" id="addSupplierRowBtn-info" class="btn btn-sm btn-secondary add-supplier-data-btn mt-2">Add
        Supplier</button>
</div>
