{{-- Location Entry Modal --}}
<x-modals.parthub-modal
    modalId="mLocationEntry"
    backdrop="static"
    keyboard="false"
    size="modal-dialog"
    modalTitleId="locationEntryModalTitle"
    title="Add New Location"
    cancelText="Cancel"
    submitText="Add Location"
    submitButtonId="addLocation">

    <span id="locationEntryText">Add new location to database</span>
    <?php echo $location_name; ?><br><br>
    <form id="locationEntryForm">
        @csrf
        <input class="form-control form-control-sm" placeholder="Location Name" id="addLocationName" autocomplete="off" required><br>
        <div class="row">
            <div class="col">
                <input class="form-control form-control-sm" placeholder="Location Description" id="addLocationDescription" autocomplete="off"><br>
            </div>
        </div>
    </form>
</x-modals.parthub-modal>
