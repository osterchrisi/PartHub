<div class="container-fluid">

    <div class="row">
        <div class="container-fluid">
            <div class="row">
                <form id="bomImportForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mt-3">
                        <div class="row">
                            <h5>Import BOM</h2>
                                <div class="col">
                                    <input class="form-control form-control-sm" id="bom_name" name="bom_name"
                                        placeholder="BOM Name" required>
                                    <x-input-error :messages="$errors->get('bom_name')" />
                                </div>
                                <div class="col">
                                    <input class="form-control form-control-sm" id="bom_description"
                                        name="bom_description" placeholder="BOM Description">
                                    <x-input-error :messages="$errors->get('bom_description')" />
                                </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col">
                                <input class="form-control form-control-sm" type="file" id="formFile"
                                    name="bom_file">
                                <small class="text-muted">Accepted file formats: ods, csv, xls, xlsx, ...</small>
                                <x-input-error :messages="$errors->get('bom_file')" />
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-sm btn-primary">Upload</button>
                    </div>
                    <div class="mt-3">
                        <div id="response-message"></div>
                    </div>
                </form>
            </div>
        </div>

    </div>
    <hr>
    <div class="row">
        <h6> Example table struture</h6>
        <div class="col" id="bom_import_example_table">
            <table class="table table-hover table-sm table-bordered" style="font-size:12px">
                <thead>
                </thead>
                <tbody>
                    <tr>
                        <td style="background-color: var(--bs-table-striped-bg);">1</td>
                        <th style="background-color: var(--bs-table-striped-bg);">Part ID</th>
                        <th style="background-color: var(--bs-table-striped-bg);">Part Name</th>
                        <th style="text-align:right; background-color: var(--bs-table-striped-bg);">Quantity</th>
                    </tr>
                    <tr>
                        <td style="background-color: var(--bs-table-striped-bg);">2</td>
                        <td>335</td>
                        <td></td>
                        <td style="text-align:right">7</td>
                    </tr>
                    <tr>
                        <td style="background-color: var(--bs-table-striped-bg);">3</td>
                        <td>337</td>
                        <td></td>
                        <td style="text-align:right">9</td>
                    </tr>
                    <tr>
                        <td style="background-color: var(--bs-table-striped-bg);">4</td>
                        <td></td>
                        <td>LM7805</td>
                        <td style="text-align:right">1</td>
                    </tr>
                    <tr>
                        <td style="background-color: var(--bs-table-striped-bg);">5</td>
                        <td></td>
                        <td>TL074</td>
                        <td style="text-align:right">16</td>
                    </tr>
                    <tr>
                        <td style="background-color: var(--bs-table-striped-bg);">6</td>
                        <td>712</td>
                        <td>NE555</td>
                        <td style="text-align:right">3</td>
                    </tr>
                    <tr>
                        <td style="background-color: var(--bs-table-striped-bg);">7</td>
                        <td>615</td>
                        <td>1N4148</td>
                        <td style="text-align:right">10</td>
                    </tr>
                    <tr>
                        <td style="background-color: var(--bs-table-striped-bg);">...</td>
                        <td>...</td>
                        <td>...</td>
                        <td style="text-align:right">...</td>
                    </tr>
                </tbody>
            </table>
            <div class="alert alert-info"> Currently no possibility to correct uploads. CSV file must contain valid
                data.</div>
        </div>
    </div>
</div>
