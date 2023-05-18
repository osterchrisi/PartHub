<div style="overflow-x:auto;">
    <table class="table table-striped table-hover table-sm" style="font-size:12px">
        <thead>
            <tr>
                @foreach ($nice_columns as $column_header)
                    <th>{{ $column_header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($stock_levels as $row)
                <tr>
                    @foreach ($column_names as $column_data)
                        @if ($column_data === 'location_name')
                            <td>{{ $row['locations']['location_name'] }}</td>
                        @else
                            <td>{{ $row[$column_data] }}</td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
