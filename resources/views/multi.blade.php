{{-- Header --}}
@include('header')

@section('page specific buttons')
    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseWidthExample">
        Toggle width collapse
    </button>
@endsection

{{-- Navbar and Toolbar --}}
@include('navbar')
@include('components.toolbarTop')




{{-- Page Contents --}}
<div class="container-fluid" id="content-container">
    <br>
    <div class="row" id="content-row">
        {{-- Filter Form --}}
        <div class="row collapse" id="parts-filter-form">
            @yield('filter-form')
        </div>

        <div class='row'>
            {{-- <div class="collapse collapse-horizontal bg-primary w-100" id="collapseWidthExample"> --}}
                {{-- Table Window --}}
                <div class='col' id='table-window' style='max-width: 90%;'>
                </div>

                {{-- Info Window --}}
                <div class='col d-flex resizable sticky justify-content-center info-window pb-3' id='info-window'
                    style="position: sticky; top: 50px;">
                </div>
            {{-- </div> --}}

            {{-- Table Window 2 --}}
            {{-- <div class='col' id='table-window2'>
            </div> --}}

            {{-- Info Window 2 --}}
            {{-- <div class='col d-flex resizable sticky justify-content-center info-window pb-3' id='info-window2'
                style="position: sticky; top: 50px;">
            </div> --}}
        </div>

    </div>
</div>
