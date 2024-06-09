@php
    $title = __('Error');
@endphp

@extends('centered-layout')

@section('title', $title)

@section('content')
    {{-- <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8"> --}}
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
        {{-- </div>
    </div> --}}
@endsection
