<?php
$user = optional(auth()->user());
$user_id = $user ? $user->id : 0;
$user_name = $user ? $user->name : '';
?>

<div class="container-fluid px-0">
    <nav class="navbar navbar-expand-lg bg-gradient">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">PartHub</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('parts') ? 'active' : '' }}" href="{{ route('parts') }}">Parts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('boms') ? 'active' : '' }}" href="{{ route('boms') }}">BOMs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('locations') ? 'active' : '' }}" href="{{ route('locations') }}">Storage Locations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('suppliers') ? 'active' : '' }}" href="{{ route('suppliers') }}">Suppliers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('footprints') ? 'active' : '' }}" href="{{ route('footprints') }}">Footprints</a>
                    </li>
                    @subscribed('maker')
                    maker
                    @endsubscribed
                </ul>
                <div class="d-flex">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li>
                            <a class="nav-link {{ request()->routeIs('whatis') ? 'active' : '' }}" href="{{ route('whatis') }}">What is PartHub?</a>
                        </li>
                        @unless (Auth::check())
                            {{-- Show Sign Up Link / Demo button if not authorized (not signed in) --}}
                            <li class="me-2 mb-2">
                                <a class="btn btn-warning {{ request()->routeIs('signup') ? 'active' : '' }}" href="{{ route('signup') }}" id="continueDemoLink">Create Account</a>
                            </li>
                            <li>
                                <form id="demoLoginButton" action="{{ route('demo.login') }}" method="GET">
                                    @csrf
                                    <button type="submit" class="btn btn-warning" id="continueDemo">Try Demo</button>
                                </form>
                            </li>
                        @endunless
                        @if ($user_id == 1)
                            {{-- Show Sign Up Link if demo user (signed in) --}}
                            <li>
                                <a class="btn btn-warning {{ request()->routeIs('signup') ? 'active' : '' }}" href="{{ route('signup') }}" id="continueDemoLink">Create Account</a>
                            </li>
                        @endif
                        <li>
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"><i class="fas fa-user"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end text-end w-auto px-2 navbar-dropdown-colored">
                                @if (Auth::check())
                                    <!-- Logged in -->
                                    <li class="navbar-user">{{ $user_name }}</li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="nav-link" href="{{ route('dashboard') }}">User Settings</a></li>
                                    <li><a class="nav-link" href="{{ route('dashboard') }}">Profile</a></li>
                                    <li><a class="nav-link" href="{{ route('dashboard') }}">Subscription</a></li>
                                    <li>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <div class="d-grid justify-content-md-end">
                                                <button type="submit" class="nav-link nav-link-button">Log Out</button>
                                            </div>
                                        </form>
                                    </li>
                                @else
                                    <!-- Not logged in -->
                                    <li><a class="nav-link" href="/login">Log In</a></li>
                                @endif
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</div>

