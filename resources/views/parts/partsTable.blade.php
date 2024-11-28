{{-- Placeholder Wrapper --}}
<div id="parts_table_placeholder">
    <table class="table table-sm table-hover table-striped w-100" style="font-size:12px">
        <thead>
            <tr class="row">
                <th class="placeholder col"></th>
                <th class="placeholder col">Name</th>
                <th class="placeholder col">Description</th>
                <th class="placeholder col">Comment</th>
                <th class="placeholder col">Category</th>
                <th class="placeholder col">Total Stock</th>
                <th class="placeholder col">Footprint</th>
                <th class="placeholder col">Unit</th>
                <th class="placeholder col">ID</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < 10; $i++)
                <tr class="placeholder-glow row">
                    <td class="placeholder bg-secondary col-1 mx-1 my-1 py-2"></td>
                    <td class="placeholder bg-secondary col mx-1 my-1 py-2"></td>
                    <td class="placeholder bg-secondary col-4 mx-1 my-1 py-2"></td>
                    <td class="placeholder bg-secondary col mx-1 my-1 py-2"></td>
                    <td class="placeholder bg-secondary col-1 mx-1 my-1 py-2"></td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>

{{-- Parts Table --}}
<table class="table table-sm table-responsive table-hover table-striped d-none" style="font-size:12px" id="parts_table"
    data-resizable="true" data-search="true" data-search-time-out="" data-search-selector="#filter"
    data-search-align="left" data-pagination="true" data-show-columns="true" {{-- data-reorderable-columns="true"  --}} data-cookie="true"
    data-cookie-id-table="PartsTableState" data-cookie-storage="localStorage" data-max-moving-rows="100"
    data-multiple-select-row="true" data-click-to-select="true">

    <thead>
        <tr>
            {{-- This first column is for Bootstrap Table Click-To-Select to work --}}
            <th data-field="state" data-checkbox="true"></th>
            @foreach ($nice_columns as $column_header)
                @if ($column_header == 'Total Stock')
                    <th data-sortable="true" data-field="{{ $column_header }}">{{ $column_header }}</th>
                @else
                    <th data-sortable="true" data-field="{{ $column_header }}">{{ $column_header }}</th>
                @endif
            @endforeach
        </tr>
    </thead>

    <tbody>
        @foreach ($parts as $part)
            @php
                $part_id = $part['part_id'];
            @endphp
            <tr data-id="{{ $part_id }}">
                @foreach ($db_columns as $column_data)
                    {{-- Total Stock --}}
                    @if ($column_data == 'total_stock')
                        <td style="text-align:right" data-id="{{ $part_id }}" data-column="{{ $column_data }}"
                            data-table_name="{{ $table_name }}" data-id_field="{{ $id_field }}">
                            {{ $part['total_stock'] }}</td>
                        {{-- Category --}}
                    @elseif ($column_data == 'category_name')
                        <td data-editable="true" class="editable editable-category" data-id="{{ $part_id }}"
                            data-column="{{ $column_data }}" data-table_name="{{ $table_name }}"
                            data-id_field="{{ $id_field }}"
                            data-category="{{ $part['category'][$column_data] ?? '' }}">
                            <x-tables.td-editable-flexbox :content="$part['category'][$column_data] ?? ''">
                                <x-tables.edit-pen />
                            </x-tables.td-editable-flexbox>
                        </td>
                        {{-- Unit --}}
                    @elseif ($column_data == 'unit_name')
                        <td data-editable="true" class="editable editable-unit" data-id="{{ $part_id }}"
                            data-column="{{ $column_data }}" data-table_name="{{ $table_name }}"
                            data-id_field="{{ $id_field }}">
                            <x-tables.td-editable-flexbox :content="$part['unit'][$column_data] ?? ''">
                                <x-tables.edit-pen />
                            </x-tables.td-editable-flexbox>
                        </td>
                        {{-- Footprint --}}
                    @elseif ($column_data == 'footprint_name')
                        <td data-editable="true" class="editable editable-footprint" data-id="{{ $part_id }}"
                            data-column="{{ $column_data }}" data-table_name="{{ $table_name }}"
                            data-id_field="{{ $id_field }}">
                            <x-tables.td-editable-flexbox :content="$part['footprint'][$column_data] ?? ''">
                                <x-tables.edit-pen />
                            </x-tables.td-editable-flexbox>
                        </td>
                        {{-- Selected / State  --}}
                    @elseif ($column_data == 'state')
                        <td></td>
                        {{-- ID --}}
                    @elseif ($column_data == 'part_id')
                        <td data-id="{{ $part_id }}" data-column="{{ $column_data }}"
                            data-table_name="{{ $table_name }}" data-id_field="{{ $id_field }}">
                            {{ $part[$column_data] }}</td>
                        {{-- 'Simple' (text-only) Fields --}}
                    @else
                        <td data-editable="true" class="editable editable-text" data-id="{{ $part_id }}"
                            data-column="{{ $column_data }}" data-table_name="{{ $table_name }}"
                            data-id_field="{{ $id_field }}">
                            <x-tables.td-editable-flexbox :content="$part[$column_data] ?? ''">
                                <x-tables.edit-pen />
                            </x-tables.td-editable-flexbox>
                        </td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>

</table>
