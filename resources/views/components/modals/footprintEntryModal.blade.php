<div class="modal fade" id="mFootprintEntry" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="footprintEntryModalTitle">Add New Footprint</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body mx-1">
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
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-primary" id="addFootprint">Add Footprint</button>
            </div>
        </div>
    </div>
</div>
