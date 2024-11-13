{{-- Category Entry Modal --}}
<x-modals.parthub-modal 
    modalId="mCategoryEntry" 
    backdrop="true" 
    keyboard="true"
    size="modal-dialog"
    modalTitleId="categoryEntryModalTitle"
    title="Add New Category"
    cancelText="Cancel"
    submitText="Add Category"
    submitButtonId="addCategory">

    <span id="categoryEntryText">Add new Category</span>
    <form id="categoryEntryForm">
        @csrf
        <input class="form-control form-control-sm" placeholder="Category Name" id="addCategoryName" autocomplete="off" required><br>
        <input type="hidden" id="parentCategoryId" name="parent_category">
    </form>
</x-modals.parthub-modal>
