{{-- @dump($supplierData) --}}
<div class="mt-3">
    <table class="table table-striped table-hover table-sm" style="font-size:12px" id="partSupplierDataTable"
        data-click-to-select="true" data-resizable="true">
        <thead>
            <tr> {{-- This first column is for Bootstrap Table Click-To-Select to work --}}
                <th data-field="state" data-checkbox="true"></th>
                @foreach ($nice_supplierDataTableHeaders as $column_header)
                    <th>{{ $column_header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($supplierData as $row)
                <tr  data-part-id="{{ $part['part_id'] }}">
                    @foreach ($supplierDataTableHeaders as $column_data)
                        @if ($column_data === 'supplier_id_fk')
                            <td data-editable="true" class="editable supplierData" data-id="{{ $row->id }}"
                                data-column="{{ $column_data }}" data-table_name="{{ $supplierDataTableName }}"
                                data-id_field="{{ $supplierDataTableIdField }}">

                                {{ $row->supplier->supplier_name }}</td>
                        @elseif ($column_data == 'state')
                            <td></td>
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
