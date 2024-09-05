<div>
    <table class="table table-sm table-responsive table-hover table-striped"
            style="font-size:12px"
            id="bomRunsTable"
            data-resizable="true"
            data-search="true"
            data-search-align="left"
            data-show-columns="true"
            {{-- data-reorderable-columns="true" --}}
            data-cookie="true"
            data-cookie-id-table="BomRunsTable"
            data-max-moving-rows="100">
        <thead>
            <tr>
                @foreach ($nice_bomRunsTableHeaders as $column_header)
                    <th data-field="{{ $column_header }}">{{ $column_header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($bom_runs as $run)
                @php
                    $bom_run_id = $run->bom_run_id;
                @endphp
                <tr data-id="{{ $run->bom_run_id }}">
                    @foreach ($bomRunsTableHeaders as $column_data)
                    @if ($column_data == 'name')
                    <td data-editable="true" data-id="{{ $bom_run_id }}" data-column="{{ $column_data }}">{{ $run->user->$column_data }}</td>
                    @endif
                        <td data-editable="true" data-id="{{ $bom_run_id }}" data-column="{{ $column_data }}">{{ $run->$column_data }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
