{{-- @dump($alternativeData) --}}
<form id="partAlternativeTableForm">
    <div class="mt-3">
        <table class="table table-striped table-hover table-sm" style="font-size:12px" id="partAlternativeTable"
            data-single-select="true" data-resizable="true">
            <thead>
                <tr> {{-- This first column is for Bootstrap Table Click-To-Select to work --}}
                    <th data-field="state" data-checkbox="true"></th>
                    @foreach ($nice_alternativeDataTableHeaders as $column_header)
                    <th>{{ $column_header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($alternativeData as $row)
                {{-- @dump($row->pivot) --}}
                <tr data-part-id="{{ $part['part_id'] }}" data-alternative-data-id="{{ $row->pivot->id }}">
                    @foreach ($alternativeDataTableHeaders as $column_data)
                    {{-- Alternative --}}
                    @if ($column_data === 'alternative_part_id')
                    <td data-editable="true" class="editable editable-alternativePart" data-id="{{ $row->pivot->id }}"
                        data-column="{{ $column_data }}" data-table_name="{{ $alternativeDataTableName }}"
                        data-id_field="{{ $alternativeDataTableIdField }}">
                        <x-tables.td-editable-flexbox :content="$row->part_name ?? ''">
                            <x-tables.edit-pen />
                        </x-tables.td-editable-flexbox>

                    </td>
                    @elseif ($column_data == 'state')
                    <td></td>
                    @endif
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
        <div id="error-alternative" class="d-none text-danger">
            <x-input-error :messages="[]" />
        </div>
        </div>
        <button type="button" id="addAlternativeRowBtn-info"
            class="btn btn-sm btn-secondary add-alternative-data-btn mt-2">Add
            Alternative</button>
        <button type="button" id="deleteAlternativeRowBtn-info"
            class="btn btn-sm btn-secondary remove-alternative-data-btn mt-2 ms-2" disabled>Delete
            Alternative</button>
    </div>
</form>