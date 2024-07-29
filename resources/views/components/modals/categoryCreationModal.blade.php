<!-- Category Creation Modal -->
<div class="modal fade" id="categoryCreationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="partEntryModalTitle">Create New Category</h1>
                <button type="button" class="btn-close" id="closeCategoryModalButton1" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="categoryCreationForm">
                    <div class="form-group">
                        <label for="categoryName">Category Name</label>
                        <input type="text" class="form-control" id="categoryName" required>
                    </div>
                    <div class="form-group">
                        <label for="parentCategory">Parent Category</label>
                        <select class="form-control" id="parentCategory" required>
                            <!-- Options will be dynamically populated -->
                        </select>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeCategoryModalButton2" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveCategoryButton">Save Category</button>
            </div>
        </div>
    </div>
</div>