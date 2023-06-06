@extends('app')

@section('info-window')
    <div class="container-fluid">
        <div class="row">
            <form action="{{ route('bom.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mt-3">
                    <div class="row"><h5>Import BOM</h2>
                        <div class="col">
                            <input class="form-control form-control-sm" id="bom_name" name="bom_name" placeholder="BOM Name"
                                required>
                            <x-input-error :messages="$errors->get('bom_name')" />
                        </div>
                        <div class="col">
                            <input class="form-control form-control-sm" id="bom_description" name="bom_description"
                                placeholder="BOM Description">
                            <x-input-error :messages="$errors->get('bom_description')" />
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <input class="form-control form-control-sm" type="file" id="formFile" name="bom_file">
                            <small class="text-muted">Accepted file formats: ods, csv, xls, xlsx, ...</small>
                            <x-input-error :messages="$errors->get('bom_file')" />
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-sm btn-primary">Upload</button>
                </div>
                <div class="mt-3">
                    @if (Session::has('success'))
                        <div class="alert alert-success">
                            {{ Session::get('success') }}
                        </div>
                    @endif

                    @if (Session::has('error'))
                        <div class="alert alert-danger">
                            {{ Session::get('error') }}
                        </div>
                    @endif
                </div>
            </form>
        </div>
        <hr>
        <div class="row">
            <h6> Example file struture</h6>
        </div>
    </div>
@endsection
