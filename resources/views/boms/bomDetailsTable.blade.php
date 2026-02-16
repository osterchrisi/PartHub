@php
    use App\Models\StockLevel;
@endphp

<div id="bomDetailsToolbar" class="d-flex align-items-center me-2">
    {{-- BOM Revision Picker --}}
    <select class="form-select form-select-sm w-auto" id="bomRevisionPicker" data-bom-id="{{ $bom_id }}"
        style="max-width: 360px;">

        @foreach ($revisions as $rev)
            @php
                $label = $rev->label ? " ({$rev->label})" : '';
                $date = $rev->created_at ? $rev->created_at->format('Y-m-d H:i') : '';
            @endphp
            <option value="{{ $rev->bom_revision_id }}" @selected($rev->bom_revision_id == $selected_revision_id)>
                Rev {{ $rev->revision_number }}{!! $label !!} ({{ $date }})
            </option>
        @endforeach
    </select>
    {{-- Revision Notice --}}
    @if ($is_viewing_old_revision ?? false)
        <div class="badge rounded-pill alert alert-warning small py-1 px-2 mb-0 ms-2" id="bomRevisionNotice">
           Old revision
        </div>
    @else
        <div class="badge rounded-pill alert alert-primary small py-1 px-2 mb-0 ms-2" id="bomRevisionNotice">
            Latest revision
        </div>
    @endif
</div>


<div>
    <table class="table table-sm table-responsive table-hover table-striped" style="font-size:12px" id="BomDetailsTable"
        data-resizable="true" data-search="true" data-search-align="left" data-show-columns="true" {{-- data-reorderable-columns="true" --}}
        data-cookie="true" data-cookie-id-table="BomDetailsTableState" data-max-moving-rows="100"
        data-toolbar="#bomDetailsToolbar">
        <thead>
            <tr>
                @foreach ($nice_columns as $column_header)
                    @if ($column_header == 'Quantity needed' || $column_header == 'Total stock available')
                        <th data-halign="right" data-field="{{ $column_header }}">{{ $column_header }}</th>
                    @elseif ($column_header == 'Can build')
                        <th data-halign="right" data-field="{{ $column_header }}" data-sortable="true">
                            {{ $column_header }}</th>
                    @else
                        <th data-field="{{ $column_header }}">{{ $column_header }}</th>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($bom_elements as $row)
                @php
                    $part_id = $row->part->part_id;
                    $part_name = $row->part->part_name;
                    $bom_element_id = $row->bom_revision_element_id;
                    $stock_levels = StockLevel::getStockLevelsByPartID($part_id);
                    $total_stock = \calculateTotalStock($stock_levels);
                @endphp
                <tr data-part-id="{{ $row->part->part_id }}" data-bom-element-id="{{ $bom_element_id }}">
                    @foreach ($db_columns as $column_data)
                        @if ($column_data == 'stock_available')
                            <td style="text-align:right">
                                {{-- This is the popover mini stock table --}}
                                <a tabindex="0" role="button" data-bs-trigger="focus" data-bs-toggle="popover"
                                    data-bs-title="Stock for {{ $part_name }}" data-bs-html="true"
                                    data-bs-content="{{ \buildHTMLTable(['location_name', 'stock_level_quantity'], ['Location', 'Quantity'], $stock_levels) }}"
                                    data-bs-sanitize="false" href="#">{{ $total_stock }}</a>
                            </td>
                        @elseif ($column_data == 'quantity')
                            {{-- Quantity Needed --}}
                            <td style="text-align:right" class="editable editable-text" data-id="{{ $bom_element_id }}"
                                data-column="{{ $column_data }}" data-table_name="bom_revision_elements"
                                data-id_field="bom_revision_element_id">
                                <x-tables.td-editable-flexbox :content="$row[$column_data] ?? ''" alignment="right">
                                    <x-tables.edit-pen />
                                </x-tables.td-editable-flexbox>
                            </td>
                        @elseif ($column_data == 'can_build')
                            @php
                                // This if statement is here in case someone changes the Quantity Needed to 0...
                                if ($row->quantity != 0) {
                                    $can_build = floor($total_stock / $row->quantity);
                                } else {
                                    $can_build = 0;
                                }
                            @endphp
                            <td style="text-align:right" data-id="{{ $part_id }}"
                                data-column="{{ $column_data }}">{{ $can_build }}</td>
                        @elseif ($column_data == 'part_name')
                            <td data-id="{{ $part_id }}" data-column="{{ $column_data }}">
                                {{ $row->part->$column_data }}</td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
