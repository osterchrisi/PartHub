{{-- Header --}}
@include('header')

@section('page specific buttons')
    <button class="btn btn-primary" type="btn btn-sm" data-bs-toggle="collapse" data-bs-target="#partsCollapse">
        Toggle Parts
    </button>
    <button class="btn btn-primary" type="btn btn-sm" data-bs-toggle="collapse" data-bs-target="#bomsCollapse">
        Toggle BOMs
    </button>
@endsection

{{-- Navbar and Toolbar --}}
@include('navbar')
@include('components.toolbarTop')


For this shenanigans to work, one needs to change:
- rebuildBomListTable: Change $('#table-window').html(data); to $('#table-only2').html(data);
- rebuiltPartsTable: Change $('#table-window').html(data); to $('#table-only').html(data);
- There's already an extra function to make the mega containers resizable (custom.js)

{{-- Page Contents --}}
<div class="container-fluid" id="content-container">
    <br>
    <div class="row">
        {{-- Filter Form --}}
        <div class="row collapse" id="parts-filter-form">
            dis filter
        </div>

        <div class="row">
            <div class ="col" id="partsMegaContainer">
                <div class="col collapse collapse-horizontal" id="partsCollapse" style='max-width: 99%;'>

                    {{-- Table Window --}}
                    <div class="row">
                        <div class='col-9' id='table-window' style='max-width: 85%;'>
                            <div id="table-only">
                            </div>
                        </div>

                        {{-- Info Window --}}
                        <div class='col d-flex sticky justify-content-center info-window pb-3'
                            id='info-window' style="position: sticky; top: 50px; height: 89vh;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col" id="bomsMegaContainer">
                <div class="col collapse collapse-horizontal" id="bomsCollapse" style='max-width: 99%;'>

                    {{-- Table Window 2 --}}
                    <div class="row">
                        <div class='col-9' id='table-window2' style='max-width: 85%;'>
                            <div id="table-only2">
                                BOMs here
                            </div>
                        </div>

                        {{-- Info Window 2 --}}
                        <div class='col d-flex sticky justify-content-center info-window pb-3'
                            id='info-window2' style="position: sticky; top: 50px; height: 89vh;">
                            BOM details here
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
