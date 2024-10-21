{{-- Stock Changing Modal --}}
<x-modals.parthub-modal
    modalId="mAddStock"
    backdrop="static"
    keyboard="false"
    size="modal-dialog"
    modalTitleId="stockModalTitle"
    title="Add Stock"
    cancelText="Cancel"
    submitText="Save changes"
    submitButtonId="AddStock">

    <span id="stockChangeText"></span>
    <span id="partName"></span>
    <br><br>
    <form method="post" action="create-part.php" id="stockChangingForm">
        @csrf
        <div class="row">
            <div class="col-3">
                <input class="form-control" placeholder="Quantity" id="addStockQuantity" required>
            </div>
        </div>
        <div class="row mt-3" id="FromStockLocationDiv-row">
            <div class="input-group" id="FromStockLocationDiv"></div>
        </div>
        <div class="row mt-3" id="ToStockLocationDiv-row">
            <div class="input-group" id="ToStockLocationDiv"></div>
        </div>
        <br>
        <input class="form-control" placeholder="Optional: Description / PO" id="addStockDescription">
    </form>
    <div class="row mt-3">
        <div id="mStockModalInfo"></div>
    </div>
</x-modals.parthub-modal>
