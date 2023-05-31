<div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="partEntryModalTitle">Assemble BOM(s)</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body mx-1">
            <div class="row">
                <span id="partEntryText">Assemble selected BOM(s)</span><br>
            </div>
            <form id="bomAssemblyForm">
                <div class="row">
                    <div class="col-3">
                        <input class="form-control" placeholder="Quantity" id="bomAssembleQuantity" required>
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
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="btnAssembleBOMs">Assemble</button>
        </div>
    </div>
</div>
