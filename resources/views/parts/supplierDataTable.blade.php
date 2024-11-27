{{-- @dump($supplierData) --}}
<form id="partSupplierDataTableForm">
    <div class="mt-3">
        <table class="table table-striped table-hover table-sm" style="font-size:12px" id="partSupplierDataTable"
            data-single-select="true" data-resizable="true">
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
                    <tr data-part-id="{{ $part['part_id'] }}" data-supplier-data-id="{{ $row->id }}">
                        @foreach ($supplierDataTableHeaders as $column_data)
                            {{-- Supplier --}}
                            @if ($column_data === 'supplier_id_fk')
                                <td data-editable="true" class="editable editable-supplierData"
                                    data-id="{{ $row->id }}" data-column="{{ $column_data }}"
                                    data-table_name="{{ $supplierDataTableName }}"
                                    data-id_field="{{ $supplierDataTableIdField }}">
                                    <x-tables.td-editable-flexbox :content="$row->supplier->supplier_name ?? ''">
                                        <x-tables.edit-pen />
                                        </x-flexbox-container>
                                        {{-- {{ $row->supplier->supplier_name }} --}}

                                </td>
                                {{-- URL --}}
                            @elseif ($column_data === 'URL')
                                <td data-editable="true" class="editable editable-text" style="max-width: 10rem"
                                    data-id="{{ $row->id }}" data-column="{{ $column_data }}"
                                    data-table_name="{{ $supplierDataTableName }}"
                                    data-id_field="{{ $supplierDataTableIdField }}">
                                    <x-tables.td-editable-flexbox :content="$row->$column_data ?? ''">
                                        <x-tables.edit-pen />
                                        </x-flexbox-container>
                                </td>
                            @elseif ($column_data == 'state')
                                <td></td>
                            @else
                                <td data-editable="true" class="editable editable-text" data-id="{{ $row->id }}"
                                    data-column="{{ $column_data }}" data-table_name="{{ $supplierDataTableName }}"
                                    data-id_field="{{ $supplierDataTableIdField }}">
                                    <x-tables.td-editable-flexbox :content="$row->$column_data ?? ''">
                                        <x-tables.edit-pen />
                                        </x-flexbox-container>
                                        {{-- {{ $row->$column_data }} --}}
                                </td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div id="error-supplier" class="d-none text-danger">
            <x-input-error :messages="[]" />
        </div>
        <div id="error-price" class="d-none text-danger">
            <x-input-error :messages="[]" />
        </div>
        <button type="button" id="addSupplierRowBtn-info"
            class="btn btn-sm btn-secondary add-supplier-data-btn mt-2">Add
            Supplier</button>
        <button type="button" id="deleteSupplierRowBtn-info"
            class="btn btn-sm btn-secondary remove-supplier-data-btn mt-2 ms-2" disabled>Delete
            Supplier</button>
    </div>
</form>
