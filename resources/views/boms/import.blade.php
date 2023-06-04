@extends('app')

@section('info-window')
    <form action="{{ route('bom.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mt-3">
            <input class="form-control form-control-sm mb-3" type="file" id="formFile" name="bom_file">
            {{-- <button type="button" class="btn btn-sm btn-primary" id="submitBomUpload" disabled>Upload</button> --}}
            {{-- <input type="file" name="bom_file"> --}}

            <div class="row">
                <div class="col">
                    <input class="form-control form-control-sm" id="bom_name" name="bom_name" placeholder="BOM Name"
                        required>
                </div>
                <div class="col">
                    <input class="form-control form-control-sm" id="bom_description" name="bom_description"
                        placeholder="BOM Description" required>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-sm btn-primary">Upload</button>
        </div>
    </form>
@endsection
