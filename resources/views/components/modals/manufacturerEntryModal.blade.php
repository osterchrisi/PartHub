<div class="modal fade" id="mManufacturerEntry" tabindex="-1" aria-labelledby="mManufacturerEntryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="mManufacturerEntryLabel">Add Manufacturer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="manufacturerEntryForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="addManufacturerName" value="{{ $manufacturer_name ?? '' }}">
                        <small class="text-danger d-none" data-error-for="manufacturer_name"></small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact person</label>
                            <input type="text" class="form-control" id="addManufacturerContactName">
                            <small class="text-danger d-none" data-error-for="manufacturer_contact_name"></small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control" id="addManufacturerEmail">
                            <small class="text-danger d-none" data-error-for="manufacturer_email"></small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" id="addManufacturerPhone">
                            <small class="text-danger d-none" data-error-for="manufacturer_phone"></small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Website</label>
                            <input type="text" class="form-control" id="addManufacturerWebsite">
                            <small class="text-danger d-none" data-error-for="manufacturer_website"></small>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Comment</label>
                        <textarea class="form-control" id="addManufacturerComment" rows="3"></textarea>
                        <small class="text-danger d-none" data-error-for="manufacturer_comment"></small>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="addManufacturer">Save</button>
            </div>

        </div>
    </div>
</div>
