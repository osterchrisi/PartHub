@extends('app')

@section('table-window')
    <link href="https://cdn.jsdelivr.net/npm/jquery-treegrid@0.3.0/css/jquery.treegrid.css" rel="stylesheet">
    <link href="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/jquery-treegrid@0.3.0/js/jquery.treegrid.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.4/dist/extensions/treegrid/bootstrap-table-treegrid.min.js">
    </script>

    @php
        echo '<pre>';
        // print_r($categories);
        echo '</pre>';
    @endphp

    <table class="table table-sm table-borderless table-responsive table-hover table-striped" style="font-size:12px" id="categories_list_table"
        data-resizable="true" data-show-columns="true" data-reorderable-columns="true" data-cookie="true"
        data-cookie-id-table="LocationsListTableState" data-cookie-storage="localStorage" data-max-moving-rows="100"
        data-tree-show-field='category_name', data-parent-id-field='parent_category'>
        <thead>
            <tr>
                <th data-field="category_id">ID</th>
                <th data-field="category_name">Category</th>
                {{-- <th data-field="parent_category">Parent Category</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr data-parent-id="{{ $category->parent_category }}">
                    <td>{{ $category->category_name }}</td>
                    <td>{{ $category->category_name }}</td>
                    {{-- <td>{{ $category->parent_category }}</td> --}}
                </tr>
                @foreach ($category->children as $child)
                    <tr data-parent-id="{{ $child->parent_category }}">
                        <td>{{ $category->category_name }}</td>
                        <td>{{ $child->category_name }}</td>
                        {{-- <td>{{ $category->parent_category }}</td> --}}
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
@endsection
