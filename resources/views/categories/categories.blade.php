@extends('app')

@section('table-window')
    <link href="https://cdn.jsdelivr.net/npm/jquery-treegrid@0.3.0/css/jquery.treegrid.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery-treegrid@0.3.0/js/jquery.treegrid.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.4/dist/extensions/treegrid/bootstrap-table-treegrid.min.js">
    </script>

    @php
        echo '<pre>';
        // print_r($categories);
        echo '</pre>';
    @endphp

    <table class="table table-sm table-borderless table-responsive table-hover table-striped" style="font-size:12px"
        id="categories_list_table" data-resizable="true" {{-- data-reorderable-columns="true" --}} data-cookie="true"
        data-cookie-id-table="CategoriesListTableState" data-cookie-storage="localStorage" data-max-moving-rows="100"
        data-parent-id-field="parent_category" data-tree-show-field="category_name" data-id-field="category_id">
        <thead>
            <tr>
                <th data-field="category_edit">Edit</th>
                <th data-field="category_name">Categories <button type="button" class="btn btn-sm btn-outline-primary" style="--bs-btn-padding-y: .05rem; --bs-btn-padding-x: .25rem; --bs-btn-font-size: .75rem;" id="cat-edit-btn">Edit</th>
                <th data-field="category_id" data-visible="false" style="display: none;">id</th>
                <th data-field="parent_category" data-visible="false" style="display: none;">parent_id</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr data-parent-id="{{ $category->parent_category }}" data-id="{{ $category->category_id }}" >
                    <td><i class="fas fa-s fa-plus"> <i class="fas fa-s fa-minus"> <i class="fas fa-s fa-pen"> <i class="fas fa-s fa-trash"></td>
                    <td>{{ $category->category_name }}</td>
                    <td style="display: none;">{{ $category->category_id }}</td>
                    <td style="display: none;">{{ $category->parent_category }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@section('info-window')
<h6><br>Click a row in the table to see category details</h6>
@endsection