<div class="modal fade" id="mSupplierEntry" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="supplierEntryModalTitle">Add New Supplier</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body mx-1">
                <span id="supplierEntryText">Add new supplier to database</span>
                <?php echo $supplier_name; ?><br><br>
                <form id="supplierEntryForm">
                    @csrf
                    <input class="form-control form-control-sm" placeholder="Supplier Name" id="addSupplierName" required><br>
                    <div class="row">
                        <div class="col">
                            <input class="form-control form-control-sm" placeholder="Supplier Alias" id="addSupplierAlias"><br>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-primary" id="addSupplier">Add Supplier</button>
            </div>
        </div>
    </div>
</div>
