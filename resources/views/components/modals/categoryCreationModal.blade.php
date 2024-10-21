{{-- Category Creation Modal --}}
<x-modals.parthub-modal 
    modalId="categoryCreationModal" 
    backdrop="static" 
    keyboard="false"
    size="modal-dialog"
    modalTitleId="partEntryModalTitle"
    title="Create New Category"
    cancelText="Close"
    submitText="Save Category"
    submitButtonId="saveCategoryButton">
    
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
</x-modals.parthub-modal>
