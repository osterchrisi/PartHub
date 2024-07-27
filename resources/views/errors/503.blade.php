@extends('errors.minimal')

@section('title', __('Service Unavailable'))
@section('code', '503')
{{-- @section('message', __('Service Unavailable - Maintenance'))
 --}}
 @section('message')
    {!! __('Service Unavailable<br><br>We are currently undergoing scheduled maintenance. Please try again later.') !!}
@endsection
