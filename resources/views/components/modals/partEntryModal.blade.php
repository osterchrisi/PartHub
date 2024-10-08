<div class="modal modal-lg fade" id="mPartEntry" tabindex="-1">
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
                    @csrf
                    {{-- Name --}}
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="addPartName" placeholder="Part Name">
                        <button class="btn btn-outline-secondary" type="button"
                            id="toggle-uppercase-button">AA</button>
                    </div><!-- Spinner container -->
                    <div id="mouserLoadingSpinner" class="d-none text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>

                    <div id="mouserSearchResults" class="mt-4"></div>

                    <div class="row">
                        <div class="col mb-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="addPartAddStockSwitch">
                                <label class="form-check-label" for="flexSwitchCheckDefault">Add Stock</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        {{-- Quantity --}}
                        <div class="col-3">
                            <input class="form-control form-control-md" placeholder="Quantity" id="addPartQuantity"
                                disabled>
                        </div>
                        {{-- Location --}}
                        <div class="col">
                            <div class="input-group" id="addPartLocDropdown"></div>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-light" id="showSuppliers" type="button" data-bs-toggle="collapse"
                        data-bs-target="#addSuppliers">Suppliers</button>
                    <button class="btn btn-sm btn-light" id="showAdvanced" type="button" data-bs-toggle="collapse"
                        data-bs-target="#advancedOptions">Additional Info</button>
                    {{-- Supplier Data --}}
                    <div class="collapse mb-4" id="addSuppliers">
                        <div class="col">
                            <div id="supplierTableContainer">
                                <table id="supplierDataTable"
                                    class="table table-sm table-bordered table-hover table-responsive">
                                    <thead>
                                        <tr>
                                            <th data-field="supplier">Supplier</th>
                                            <th data-field="URL">URL</th>
                                            <th data-field="SPN">SPN</th>
                                            <th data-field="price">Price</th>
                                            <th data-field="remove"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Rows will be added dynamically here --}}
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" id="addSupplierRowBtn-partEntry"
                                class="btn btn-sm btn-secondary add-supplier-data-btn mt-2">Add
                                Supplier</button>
                        </div>
                    </div>
                    {{-- Advanced Options --}}
                    <div class="collapse mb-4" id="advancedOptions">
                        <div class="row">
                            {{-- Minimum Quantity --}}
                            <div class="mt-3">
                                <input class="form-control form-control-sm not-required" id="addPartMinQuantity"
                                    placeholder="Minimum Quantity" data-bs-toggle="tooltip"
                                    data-bs-title="Notification threshold for all locations combined"
                                    data-bs-placement="right">
                            </div>
                            <div class="col">
                                {{-- Category --}}
                                <div class="form-floating" id="addPartCategoryDropdown">
                                </div>
                                {{-- Footprint --}}
                                <div class="col">
                                    <div class="form-floating" id="addPartFootprintDropdown">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            {{-- Description --}}
                            <div class="col">
                                <br>
                                <input class="form-control form-control-sm not-required" id="addPartDescription"
                                    placeholder="Description">
                            </div>
                        </div>
                        <div class="row">
                            {{-- Comment --}}
                            <div class="col">
                                <br>
                                <input class="form-control form-control-sm not-required" id="addPartComment"
                                    placeholder="Comment">
                            </div>
                        </div>
                    </div>
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
