{{-- Part Entry Modal --}}
<x-modals.parthub-modal modalId="mPartEntry" backdrop="static" keyboard="false" size="modal-lg"
    modalTitleId="partEntryModalTitle" title="Add New Part" cancelText="Cancel" submitText="Add Part"
    submitButtonId="addPart">

    <span id="partEntryText">Add new part to database</span>
    <?php echo $part_name; ?><br><br>
    <form id="partEntryForm">
        @csrf
        {{-- <!-- Toggle Buttons --> 
        <button class="btn btn-sm btn-light mb-1" id="manualEntryButton" type="button">Manual Entry</button>
        <button class="btn btn-sm btn-light mb-1" id="mouserSearchButton" type="button">Mouser Search</button> --}}

        <!-- Manual Entry Part Name Input -->
        <div class="input-group mb-3" id="manualEntrySection">
            <input type="text" class="form-control" id="addPartName" name="part_name" placeholder="Part Name">
            <button class="btn btn-outline-secondary" type="button" id="toggle-uppercase-button">AA</button>
        </div>
        <div id="error-part_name" class="d-none text-danger">
            <x-input-error :messages="[]" />
        </div>

        {{-- <!-- Mouser Search Section (Initially Hidden) --> 
        <div id="mouserSearchSection">
            <input type="text" class="form-control" id="mouserPartName" placeholder="Search Part on Mouser">
            <!-- Spinner container -->
            <div id="mouserLoadingSpinner" class="d-none text-center">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div id="mouserSearchResults" class="mt-4"></div>
        </div> --}}

        <div class="row">
            <div class="col mb-2">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="addPartAddStockSwitch">
                    <label class="form-check-label" for="flexSwitchCheckDefault">Add Stock</label>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            {{-- Quantity --}}
            <div class="col-3">
                <input class="form-control form-control-md" name="quantity" placeholder="Quantity" id="addPartQuantity"
                    disabled>
                <div id="error-quantity" class="d-none text-danger">
                    <x-input-error :messages="[]" />
                </div>
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
                    <table id="supplierDataTable" class="table table-sm table-bordered table-hover table-responsive">
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
                    class="btn btn-sm btn-secondary add-supplier-data-btn mt-2">Add Supplier</button>
            </div>
            <div id="error-suppliers.1.supplier_id" class="d-none text-danger">
                <x-input-error :messages="[]" />
            </div>
            <div id="error-suppliers.1.URL" class="d-none text-danger">
                <x-input-error :messages="[]" />
            </div>
            <div id="error-suppliers.1.SPN" class="d-none text-danger">
                <x-input-error :messages="[]" />
            </div>
            <div id="error-suppliers.1.price" class="d-none text-danger">
                <x-input-error :messages="[]" />
            </div>
        </div>

        {{-- Advanced Options --}}
        <div class="collapse mb-4" id="advancedOptions">
            <div class="row">
                {{-- Minimum Quantity --}}
                <div class="mt-3">
                    <input class="form-control form-control-sm not-required" name="min_quantity" id="addPartMinQuantity"
                        placeholder="Minimum Quantity" data-bs-toggle="tooltip"
                        data-bs-title="Notification threshold for all locations combined" data-bs-placement="right">
                    <div id="error-min_quantity" class="d-none text-danger">
                        <x-input-error :messages="[]" />
                    </div>
                </div>
                <div class="col">
                    {{-- Category --}}
                    <div class="form-floating" id="addPartCategoryDropdown"></div>
                    {{-- Footprint --}}
                    <div class="col">
                        <div class="form-floating" id="addPartFootprintDropdown"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                {{-- Description --}}
                <div class="col">
                    <br>
                    <input class="form-control form-control-sm not-required" name="description" id="addPartDescription"
                        placeholder="Description">
                    <div id="error-description" class="d-none text-danger">
                        <x-input-error :messages="[]" />
                    </div>
                </div>
            </div>
            <div class="row">
                {{-- Comment --}}
                <div class="col">
                    <br>
                    <input class="form-control form-control-sm not-required" name="comment" id="addPartComment"
                        placeholder="Comment">
                    <div id="error-comment" class="d-none text-danger">
                        <x-input-error :messages="[]" />
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-modals.parthub-modal>
