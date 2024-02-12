@php
// echo($user_id);
// print_r($footprints_list);
@endphp
<div>
    <table class="table table-sm table-responsive table-hover table-striped" style="font-size:12px" id="footprints_list_table"
        data-resizable="true" data-pagination="true" data-show-columns="true" 
        {{-- data-reorderable-columns="true" --}}
        data-cookie="true" data-cookie-id-table="FootprintsListTableState" data-cookie-storage="localStorage"
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
            @foreach ($footprints_list as $row)
                @php
                    $footprint_id = $row['footprint_id'];
                @endphp
                <tr data-id="{{ $row['footprint_id'] }}">
                    @foreach ($db_columns as $column_data)
                        @if ($column_data != 'state')
                            <td data-editable="true" class="editable" data-id="{{ $footprint_id }}"
                                data-column="{{ $column_data }}" data-table_name="{{ $table_name }}"
                                data-id_field="{{ $id_field }}">{{ $row[$column_data] }}</td>
                        @else
                        {{-- 'state' is empty, Bootstrap Tables will place a checkbox here --}}
                        <td></td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
