<div>
    <table class="table table-sm table-responsive table-hover table-striped"
            style="font-size:12px"
            id="partInBomsTable"
            data-resizable="true"
            data-search="true"
            data-search-align="left"
            data-show-columns="true"
            {{-- data-reorderable-columns="true" --}}
            data-cookie="true"
            data-cookie-id-table="PartInBomsTableState"
            data-max-moving-rows="100">
        <thead>
            <tr>
                @foreach ($nice_bomTableHeaders as $column_header)
                    <th data-field="{{ $column_header }}">{{ $column_header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($bom_list as $row)
                @php
                    $bom_id = $row['bom_id'];
                @endphp
                <tr data-id="{{ $row['bom_elements_id'] }}">
                    @foreach ($bomTableHeaders as $column_data)
                        <td data-editable="true"
                        data-id="{{ $bom_id }}" data-column="{{ $column_data }}" data-table_name="bom_elements" data-id_field="bom_elements_id">{{ $row[$column_data] }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
