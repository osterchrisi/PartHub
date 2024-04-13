@extends('app')

@section('table-window')
@include('categories.categoriesTable')
@endsection

@section('info-window')
    <h6><br>Click a row in the table to see category details</h6>
@endsection
