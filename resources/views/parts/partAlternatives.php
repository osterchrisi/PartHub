{{-- @dump($alternativeData) --}}
<form id="partAlternativeTableForm">
    <div class="mt-3">
        <table class="table table-striped table-hover table-sm" style="font-size:12px" id="partAlternativeDataTable"
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
                    <tr data-part-id="{{ $part['part_id'] }}" data-alternative-data-id="{{ $row->id }}">
                        @foreach ($alternativeDataTableHeaders as $column_data)
                            {{-- Alternative --}}
                            @if ($column_data === 'alternative_id_fk')
                                <td data-editable="true" class="editable editable-alternativeData"
                                    data-id="{{ $row->id }}" data-column="{{ $column_data }}"
                                    data-table_name="{{ $alternativeDataTableName }}"
                                    data-id_field="{{ $alternativeDataTableIdField }}">
                                    <x-tables.td-editable-flexbox :content="$row->alternative->alternative_name ?? ''">
                                        <x-tables.edit-pen />
                                    </x-tables.td-editable-flexbox>

                                </td>
                                {{-- URL --}}
                            @elseif ($column_data === 'URL')
                                <td data-editable="true" class="editable editable-text" style="max-width: 10rem"
                                    data-id="{{ $row->id }}" data-column="{{ $column_data }}"
                                    data-table_name="{{ $alternativeDataTableName }}"
                                    data-id_field="{{ $alternativeDataTableIdField }}">
                                    <x-tables.td-editable-flexbox :content="$row->$column_data ?? ''" extraContentClass="text-truncate">
                                        <x-tables.copy-clipboard :content="$row->$column_data ?? ''" />
                                        <x-tables.edit-pen />
                                    </x-tables.td-editable-flexbox>
                                </td>
                            @elseif ($column_data == 'state')
                                <td></td>
                            @else
                                <td data-editable="true" class="editable editable-text" data-id="{{ $row->id }}"
                                    data-column="{{ $column_data }}" data-table_name="{{ $alternativeDataTableName }}"
                                    data-id_field="{{ $alternativeDataTableIdField }}">
                                    <x-tables.td-editable-flexbox :content="$row->$column_data ?? ''">
                                        <x-tables.edit-pen />
                                    </x-tables.td-editable-flexbox>
                                </td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div id="error-alternative" class="d-none text-danger">
            <x-input-error :messages="[]" />
        </div>
        <div id="error-price" class="d-none text-danger">
            <x-input-error :messages="[]" />
        </div>
        <button type="button" id="addAlternativeRowBtn-info"
            class="btn btn-sm btn-secondary add-alternative-data-btn mt-2">Add
            Alternative</button>
        <button type="button" id="deleteAlternativeRowBtn-info"
            class="btn btn-sm btn-secondary remove-alternative-data-btn mt-2 ms-2" disabled>Delete
            Alternative</button>
    </div>
</form>
