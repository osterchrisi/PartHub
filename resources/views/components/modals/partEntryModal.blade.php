<div class="modal fade" id="mPartEntry" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="partEntryModalTitle">Add New Part</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body mx-1">
                <span id="partEntryText">Add new part to database</span>
                <?php echo $part_name; ?><br><br>
                <form id="partEntryForm">
                    <input class="form-control form-control-sm" placeholder="Part Name" id="addPartName" required><br>
                    <div class="row">
                        <div class="col-3">
                            <input class="form-control form-control-sm" placeholder="Quantity" id="addPartQuantity"
                                required>
                        </div>
                        <div class="col">
                            <div class="input-group" id="addPartLocDropdown"></div>
                        </div>
                    </div>
                    <br>
                    <button class="btn btn-sm" id="showAdvanced" type="button" data-bs-toggle="collapse"
                        data-bs-target="#advancedOptions">Show Advanced</button>
                    <div class="collapse" id="advancedOptions">
                        <div class="row">
                            <div class="col">
                                <input class="form-select form-select-sm not-required" placeholder="Category">
                            </div>
                            <div class="col">
                                <input class="form-select form-select-sm not-required" placeholder="Footprint">
                            </div>
                        </div>
                        <br>
                        <input class="form-control form-control-sm not-required" placeholder="Description">
                        <br>
                        <input class="form-control form-control-sm not-required" placeholder="Comment">
                    </div>
                    <div class="input-group"></div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-primary" id="addPart">Add Part</button>
            </div>
        </div>
    </div>
</div>
