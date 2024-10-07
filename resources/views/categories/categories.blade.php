@extends('app')

@section('table-window')
    @include('categories.categoriesTable')
@endsection

@section('info-window')
<x-info-window-info-div>
    Click a row in the table to see category details
</x-info-window-info-div>
@endsection
