{{-- Footprint Entry Modal --}}
<x-modals.parthub-modal
    modalId="mFootprintEntry"
    backdrop="static"
    keyboard="false"
    size="modal-dialog"
    modalTitleId="footprintEntryModalTitle"
    title="Add New Footprint"
    cancelText="Cancel"
    submitText="Add Footprint"
    submitButtonId="addFootprint">

    <span id="footprintEntryText">Add new footprint to database</span>
    <?php echo $footprint_name; ?><br><br>
    <form id="footprintEntryForm">
        @csrf
        <input class="form-control form-control-sm" placeholder="Footprint Name" id="addFootprintName" required><br>
        <div class="row">
            <div class="col">
                <input class="form-control form-control-sm" placeholder="Footprint Alias" id="addFootprintAlias"><br>
            </div>
        </div>
    </form>
</x-modals.parthub-modal>
