@extends('app')

@section('table-window')
    <link href="https://cdn.jsdelivr.net/npm/jquery-treegrid@0.3.0/css/jquery.treegrid.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery-treegrid@0.3.0/js/jquery.treegrid.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.4/dist/extensions/treegrid/bootstrap-table-treegrid.min.js">
    </script>
    @include('categories.categoriesTable')
@endsection

@section('info-window')
    <h6><br>Click a row in the table to see category details</h6>
@endsection
