<div class="modal fade" id="mAddStock" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="stockModalTitle">Add Stock</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body mx-1">
                <span id="stockChangeText"></span>
                <span id="partName"></span>
                <br><br>
                <form method="post" action="create-part.php" id="stockChangingForm">
                    @csrf
                    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
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
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="AddStock">Save changes</button>
            </div>
        </div>
    </div>
</div>
