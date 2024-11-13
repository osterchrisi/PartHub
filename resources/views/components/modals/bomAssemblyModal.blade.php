{{-- BOM Assembly Modal --}}
<x-modals.parthub-modal 
    modalId="mBomAssembly" 
    backdrop="static" 
    keyboard="false"
    size="modal-dialog"
    modalTitleId="partEntryModalTitle"
    title="Assemble BOM(s)"
    cancelText="Cancel"
    submitText="Assemble"
    submitButtonId="btnAssembleBOMs">
    
    <div class="row">
        <span id="partEntryText">Assemble selected BOM(s)</span><br>
    </div>
    <form id="bomAssemblyForm">
        @csrf
        <div class="row">
            <div class="col-3">
                <input class="form-control" placeholder="Quantity" id="bomAssembleQuantity" autocomplete="off" required>
                <br>
            </div>
        </div>
        <div class="row">
            <div class="input-group" id="bomAssembleLocationDiv"></div>
        </div>
    </form>
    <div class="row mt-3">
        <div id="mBomAssemblyInfo"></div>
    </div>
</x-modals.parthub-modal>