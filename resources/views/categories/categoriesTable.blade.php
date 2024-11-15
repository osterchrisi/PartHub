<div id="categories_list_table_placeholder" class="mt-3 me-4 mb-3 w-auto">
    <table class="table table-sm table-hover table-striped table-bordered table-responsive w-100 w-auto" style="font-size:12px; min-width:15rem;">
        <thead>
            <tr>
                <th class="placeholder">Categories</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <p class="placeholder-glow my-0"><span class="treegrid-expander treegrid-expander-expanded"></span>
                        <span class="placeholder col-3"></span>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="placeholder-glow my-0"><span class="treegrid-indent"></span><span
                            class="treegrid-expander"></span>
                        <span class="placeholder col-4"></span>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="placeholder-glow my-0"><span class="treegrid-indent"></span><span
                            class="treegrid-indent"></span><span
                            class="treegrid-expander treegrid-expander-expanded"></span>
                        <span class="placeholder col-5"></span>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="placeholder-glow my-0"><span class="treegrid-indent"></span><span
                            class="treegrid-indent"></span><span class="treegrid-expander"></span><span
                            class="placeholder col-3"></span>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="placeholder-glow my-0"><span class="treegrid-indent"></span><span
                            class="treegrid-indent"></span><span class="treegrid-expander"></span><span
                            class="placeholder col-4"></span>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="placeholder-glow my-0"><span class="treegrid-indent"></span><span
                            class="treegrid-indent"></span><span class="treegrid-expander"></span><span
                            class="placeholder col-3"></span>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="placeholder-glow my-0"><span class="treegrid-indent"></span><span
                            class="treegrid-expander"></span>
                        <span class="placeholder col-4"></span>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="placeholder-glow my-0"><span class="treegrid-indent"></span><span
                            class="treegrid-expander"></span>
                        <span class="placeholder col-5"></span>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<table class="table table-sm table-borderless table-responsive table-hover table-striped w-auto mt-3 me-3 mb-3 d-none"
    style="font-size:12px" id="categories_list_table" data-resizable="true" {{-- data-reorderable-columns="true" --}} data-cookie="true"
    data-cookie-id-table="CategoriesListTableState" data-cookie-storage="localStorage" data-max-moving-rows="100"
    data-parent-id-field="parent_category" data-tree-show-field="category_name" data-id-field="category_id">
    <thead>
        <tr>
            <th data-field="category_edit"></th>
            <th data-field="category_name">
                <div class="row">
                    <div class="col-auto me-auto"><span>Categories</span></div>
                    <div class="col-auto pe-1 me-0">
                        <button id="category-toggle-collapse-expand"
                            class="btn btn-extra-sm btn-outline-secondary">Toggle</button>
                    </div>
                    <div class="col-auto ms-0 ps-0"><button type="button"
                            class="btn btn-extra-sm btn-outline-secondary" id="cat-edit-btn">Edit</button></div>
                </div>
            </th>
            <th data-field="category_id" data-visible="false" style="display: none;">id</th>
            <th data-field="parent_category" data-visible="false" style="display: none;">parent_id</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($categoriesForCategoriesTable as $category)
            <tr data-parent-id="{{ $category->parent_category }}" data-id="{{ $category->category_id }}"
                data-table_name="categories">
                <td data-field="category_edit">
                    <div class="d-flex"><button type="button"
                            class="btn btn-outline-secondary edit-button addcat-button" id="cat-edit-btn"
                            data-action="add" {{-- @if (config('app.env') === 'demo') data-bs-toggle="popover"
                            data-bs-title="We're sowwy 😿"
                            data-bs-content="We currently don't allow editing of categories in the demo :( Instead, please accept this flower 🌸" @endif --}}><i class="fas fa-s fa-plus icon-small"></i></button>
                        @unless ($category->parent_category == 0)
                            <button type="button" class="btn btn-outline-secondary edit-button trash-button"
                                id="cat-edit-btn" data-action="remove" {{-- @if (config('app.env') === 'demo') data-bs-toggle="popover"
                                    data-bs-title="We're sowwy 😿"
                                    data-bs-content="We currently don't allow editing of categories in the demo :( Instead, please accept this flower 🌸" @endif --}}><i
                                    class="fas fa-s fa-trash icon-small" data-action="trash"></i></button>
                        @endunless
                    </div>
                </td>
                <td data-editable="true" class="editable editable-text" data-id="{{ $category->category_id }}"
                    data-column="category_name" data-table_name="part_categories" data-id_field="category_id">
                    <x-tables.td-editable-flexbox :content="$category->category_name ?? ''">
                        <x-tables.edit-pen />
                        </x-flexbox-container>
                </td>
                <td style="display: none;">{{ $category->category_id }}</td>
                <td style="display: none;">{{ $category->parent_category }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
