<div>
    <table  class="table table-sm table-responsive table-hover table-striped"
            style="font-size:12px" 
            id="parts_table" 
            data-resizable="true" 
            data-search="true" 
            data-search-time-out="" 
            data-search-selector="#filter" 
            data-search-align="left" 
            data-pagination="true" 
            data-show-columns="true" 
            {{-- data-reorderable-columns="true"  --}}
            data-cookie="true" 
            data-cookie-id-table="PartsTableState" 
            data-cookie-storage="localStorage" 
            data-max-moving-rows="100" 
            data-multiple-select-row="true" 
            data-click-to-select="true">

        <thead>
            <tr>
                {{-- This column is for Bootstrap Table Click-To-Select to work --}}
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
                    {{-- I am inserting '' for everything that could potentially not have a value, especially values that are potentially inside another array. Otherwise I get array offset errors --}}
                        {{-- Total Stock --}}
                        @if ($column_data == 'total_stock')
                            <td style="text-align:right" data-id="{{ $part_id }}" data-column="{{ $column_data }}" data-table_name="{{ $table_name }}" data-id_field="{{ $id_field }}">{{ $part['total_stock'] }}</td>
                        {{-- Category --}}
                        @elseif ($column_data == 'category_name')
                            <td data-editable="true" class="editable category" data-id="{{ $part_id }}" data-column="{{ $column_data }}" data-table_name="{{ $table_name }}" data-id_field="{{ $id_field }}">{{ $part['category'][$column_data] ?? '' }}</td>
                        {{-- Unit --}}
                        @elseif ($column_data == 'unit_name')
                            <td data-editable="true" class="editable category" data-id="{{ $part_id }}" data-column="{{ $column_data }}" data-table_name="{{ $table_name }}" data-id_field="{{ $id_field }}">{{ $part['unit'][$column_data] ?? '' }}</td>
                        {{-- Footprint --}}
                        @elseif ($column_data == 'footprint_name')
                            <td data-editable="true" class="editable footprint" data-id="{{ $part_id }}" data-column="{{ $column_data }}" data-table_name="{{ $table_name }}" data-id_field="{{ $id_field }}">{{ $part['footprint'][$column_data] ?? '' }}</td>
                        {{-- Selected / State  --}}
                        @elseif ($column_data == 'state')
                            <td></td>
                        {{-- ID --}}
                        @elseif ($column_data == 'part_id')
                            <td data-id="{{ $part_id }}" data-column="{{ $column_data }}" data-table_name="{{ $table_name }}" data-id_field="{{ $id_field }}">{{ $part[$column_data] }}</td>
                        {{-- 'Simple' (text-only) Fields --}}
                        @else
                            <td data-editable="true" class="editable" data-id="{{ $part_id }}" data-column="{{ $column_data }}" data-table_name="{{ $table_name }}" data-id_field="{{ $id_field }}">{{ $part[$column_data] ?? '' }}</td>                      
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>

    </table>
</div>