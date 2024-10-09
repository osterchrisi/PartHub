<div class="modal fade" id="mCategoryEntry" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="categoryEntryModalTitle">Add New Category</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body mx-1">
                <span id="categoryEntryText">Add new Category</span>
                <form id="categoryEntryForm">
                    @csrf
                    <input class="form-control form-control-sm" placeholder="Category Name" id="addCategoryName" required><br>
                    <input type="hidden" id="parentCategoryId" name="parent_category">
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-primary" id="addCategory">Add Category</button>
            </div>
        </div>
    </div>
</div>
