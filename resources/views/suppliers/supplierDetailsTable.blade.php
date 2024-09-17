<div class="mt-3">
    <table class="table table-striped table-hover table-sm" style="font-size:12px" id="supplierDetailsTable"
        data-resizable="true">
        <thead>
            <tr>
                @foreach ($nice_columns as $column_header)
                    <th>{{ $column_header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($suppliedParts as $suppliedPart)
                <tr data-supplier-id="{{ $supplier->supplier_id }}" data-part-id="{{ $suppliedPart->part->part_id }}">

                    @foreach ($db_columns as $column_data)
                        @if ($column_data === 'part_name')
                            <td>{{ $suppliedPart->part->part_name }}</td>
                        @elseif ($column_data === 'part_id')
                            <td>{{ $suppliedPart->part->part_id }}</td>
                        @else
                            <td>{{ $suppliedPart->$column_data }}</td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
