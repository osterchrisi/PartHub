
<table class="table table-sm table-borderless table-responsive table-hover table-striped w-auto" style="font-size:12px"
    id="categories_list_table" data-resizable="true" {{-- data-reorderable-columns="true" --}} data-cookie="true"
    data-cookie-id-table="CategoriesListTableState" data-cookie-storage="localStorage" data-max-moving-rows="100"
    data-parent-id-field="parent_category" data-tree-show-field="category_name" data-id-field="category_id">
    <thead>
        <tr>
            <th data-field="category_edit"></th>
            <th data-field="category_name">
                <div class="row">
                    <div class="col-auto me-auto">Categories</div>
                    <div class="col-auto"><button type="button" class="btn btn-sm btn-outline-secondary"
                            style="--bs-btn-padding-y: .05rem; --bs-btn-padding-x: .25rem; --bs-btn-font-size: .75rem;"
                            id="cat-edit-btn">Edit Categories</button></div>
            </th>
            <th data-field="category_id" data-visible="false" style="display: none;">id</th>
            <th data-field="parent_category" data-visible="false" style="display: none;">parent_id</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($categoriesForCategoriesTable as $category)
            <tr data-parent-id="{{ $category->parent_category }}" data-id="{{ $category->category_id }}"
                data-table_name="categories">
                <td data-field="category_edit"><button type="button"
                        class="btn btn-sm btn-outline-secondary edit-button addcat-button"
                        style="--bs-btn-padding-y: .05rem; --bs-btn-padding-x: .25rem; --bs-btn-font-size: .75rem;"
                        id="cat-edit-btn" data-action="add"
                        @if (config('app.env') === 'demo')
                        data-bs-toggle="popover" data-bs-title="We're sowwy 😿"
                        data-bs-content="We currently don't allow editing of categories in the demo :( Instead, please accept this flower 🌸"
                        @endif><i
                            class="fas fa-s fa-plus"></i></button>
                    @unless ($category->parent_category == 0)
                    <button type="button" class="btn btn-sm btn-outline-secondary edit-button trash-button"
                        style="--bs-btn-padding-y: .05rem; --bs-btn-padding-x: .25rem; --bs-btn-font-size: .75rem;"
                        id="cat-edit-btn" data-action="remove"
                        @if (config('app.env') === 'demo')
                        data-bs-toggle="popover" data-bs-title="We're sowwy 😿"
                        data-bs-content="We currently don't allow editing of categories in the demo :( Instead, please accept this flower 🌸"
                        @endif><i
                            class="fas fa-s fa-trash edit-icon" data-action="trash"></i></button>
                    @endunless
                    {{-- <button type="button" class="btn btn-sm btn-outline-secondary edit-button"
                        style="--bs-btn-padding-y: .05rem; --bs-btn-padding-x: .25rem; --bs-btn-font-size: .75rem;"
                        id="cat-edit-btn" data-action="edit"
                        @if (config('app.env') === 'demo')
                        data-bs-toggle="popover" data-bs-title="We're sowwy 😿"
                        data-bs-content="We currently don't allow editing of categories in the demo :( Instead, please accept this flower 🌸"
                        @endif><i
                            class="fas fa-s fa-pen" data-action="edit"></i></button> --}}
                </td>
                <td data-editable="true" class="editable" data-id="{{ $category->category_id }}"
                    data-column="category_name" data-table_name="part_categories" data-id_field="category_id">
                    {{ $category->category_name }}</td>
                <td style="display: none;">{{ $category->category_id }}</td>
                <td style="display: none;">{{ $category->parent_category }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
