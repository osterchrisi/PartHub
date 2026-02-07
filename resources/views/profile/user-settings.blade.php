{{-- Parent Template --}}
@extends('centered-layout')

@section('content')
    <div class="row justify-content-center">
        <div class="col">
            <div class="card shadow-sm mt-4 mb-4">
                <div class="card-body">
                    <h2 class="card-title text-center">User Settings</h2>
                    <hr>

                    {{-- Switch for stock level notification --}}
                    <div class="form-group mb-4">
                        <label class="form-label" for="stocklevel_notification">
                            <strong>Stock Level Notifications</strong>
                            <small class="d-block text-muted">Receive notifications via email when stock levels go below
                                threshold.</small>
                        </label>
                        <div class="form-check form-switch">
                            <input class="form-check-input user-setting-switch" type="checkbox" id="stocklevel_notification"
                                data-setting-name="stocklevel_notification">
                            <label class="form-check-label" for="stocklevel_notification">Enable Stock Notifications</label>
                        </div>
                    </div>

                    <hr>

                    {{-- Timezone Setting --}}
                    <div class="form-group mb-4">
                        <label for="timezone"><strong>Timezone</strong></label>
                        <small class="d-block text-muted mb-2">Set your preferred timezone for correct time in your
                            tables.</small>
                        <form method="POST" action="{{ route('user.settings.update') }}">
                            @csrf
                            <select name="timezone" id="timezone" class="form-control">
                                @foreach (timezone_identifiers_list() as $timezone)
                                    <option value="{{ $timezone }}" {{ $userTimezone == $timezone ? 'selected' : '' }}>
                                        {{ $timezone }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary mt-3 w-100">Save Settings</button>
                        </form>
                    </div>

                    <hr>

                    {{-- Mouser Part Search Setting --}}
                    <div class="form-group mb-4">
                        <label class="form-label">
                            <strong>Mouser Part Search</strong>
                            <small class="d-block text-muted">Use your own Mouser API key to enable part lookup.</small>
                        </label>

                        <form method="POST" action="{{ route('user.settings.update') }}">
                            @csrf

                            <div class="form-check form-switch mb-2">
                                {{-- HTML checkboxes only submit a value when checked. To make “off” submit cleanly --}}
                                {{-- TODO: Verify and remove is not applicable --}}
                                <input type="hidden" name="mouser_enabled" value="0">

                                <input class="form-check-input" type="checkbox" id="mouser_enabled" name="mouser_enabled"
                                    value="1" {{ $mouserEnabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="mouser_enabled">Enable Mouser Search</label>
                            </div>

                            <input type="text" class="form-control" name="mouser_api_key" id="mouser_api_key"
                                placeholder="{{ $hasMouserKey ? 'Key stored (enter to replace)' : 'Enter your Mouser API key' }}"
                                {{ $mouserEnabled ? '' : 'disabled' }}>

                            <button type="submit" class="btn btn-primary mt-3 w-100">Save Settings</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('modals and menus')
@endsection
