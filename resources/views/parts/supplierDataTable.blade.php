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
                        <td>{{ $row[$column_data] }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
