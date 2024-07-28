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
                    {{-- <input class="form-control form-control-sm" placeholder="Part Name" id="addPartName" required><br> --}}
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="addPartName" placeholder="Part Name">
                        <button class="btn btn-outline-secondary" type="button"
                            id="toggle-uppercase-button">AA</button>
                    </div>

                    <div class="row">
                        {{-- Quantity --}}
                        <div class="col-3">
                            <input class="form-control form-control-md" placeholder="Quantity" id="addPartQuantity"
                                required>
                        </div>
                        {{-- Location --}}
                        <div class="col">
                            <div class="input-group" id="addPartLocDropdown"></div>
                        </div>
                    </div>
                    <br>
                    <button class="btn btn-sm btn-light" id="showAdvanced" type="button" data-bs-toggle="collapse"
                        data-bs-target="#advancedOptions">Show Advanced</button>
                    {{-- Advanced Options --}}
                    <div class="collapse mb-4" id="advancedOptions">
                        <div class="row">
                            {{-- Minimum Quantity --}}
                            <div>
                                <br>
                                <input class="form-control form-control-sm not-required" id="addPartMinQuantity"
                                    placeholder="Minimum Quantity" data-bs-toggle="tooltip"
                                    data-bs-title="Notification threshold for all locations combined"
                                    data-bs-placement="right">
                            </div>
                            {{-- Category --}}
                            <div class="col">
                                <div class="form-floating" id="addPartCategoryDropdown">
                                </div>
                                {{-- Footprint --}}
                                <div class="col">
                                    <div class="form-floating" id="addPartFootprintDropdown">
                                    </div>
                                </div>
                                {{-- Supplier --}}
                                <div class="col">
                                    <div class="form-floating" id="addPartSupplierDropdown">
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
