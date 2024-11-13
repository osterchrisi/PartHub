{{-- Supplier Entry Modal --}}
<x-modals.parthub-modal
    modalId="mSupplierEntry"
    backdrop="static"
    keyboard="false"
    size="modal-dialog"
    modalTitleId="supplierEntryModalTitle"
    title="Add New Supplier"
    cancelText="Cancel"
    submitText="Add Supplier"
    submitButtonId="addSupplier">

    <span id="supplierEntryText">Add new supplier to database</span>
    <?php echo $supplier_name; ?><br><br>
    <form id="supplierEntryForm">
        @csrf
        <input class="form-control form-control-sm" placeholder="Supplier Name" id="addSupplierName" autocomplete="off" required><br>
    </form>
</x-modals.parthub-modal>
