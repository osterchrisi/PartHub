<div class="mt-3" style="overflow-x:auto;">
    <table class="table table-striped table-hover table-sm" style="font-size:12px">
        <thead>
            <tr>
                @foreach ($nice_supplierDataTableHeaders as $column_header)
                    <th>{{ $column_header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($supplierData as $row)
                <tr>
                    @foreach ($supplierDataTableHeaders as $column_data)
                        @if ($column_data === 'supplier_id_fk')
                            <td>{{ $row->supplier->supplier_name }}</td>
                        @else
                            <td>{{ $row->$column_data }}</td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
