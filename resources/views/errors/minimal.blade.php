@php
    $title = __('Error');
@endphp

@extends('centered-layout')

@section('title', $title)

@section('content')
    <div class="flex items-center pt-8 sm:justify-start sm:pt-0">
        <div class="text-center">
            <h1>
                @yield('code')
            </h1>
        </div>

        <div class="text-center">
            <h4>
                @yield('message')
            </h4>
        </div>
    </div>
@endsection
