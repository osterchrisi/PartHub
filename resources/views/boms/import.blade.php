@extends('app')

@section('info-window')
    <div class="row">
        <form action="{{ route('bom.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mt-3">
                <input class="form-control form-control-sm mb-3" type="file" id="formFile" name="bom_file">
                <x-input-error :messages="$errors->get('bom_file')" />
                {{-- <button type="button" class="btn btn-sm btn-primary" id="submitBomUpload" disabled>Upload</button> --}}
                {{-- <input type="file" name="bom_file"> --}}

                <div class="row">
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
@endsection
