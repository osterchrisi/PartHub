<?php
$user = optional(auth()->user());
$user_id = $user ? $user->id : 0;
$user_name = $user ? $user->name : '';
?>

<div class="container-fluid px-0">
    <nav class="navbar navbar-expand-lg bg-primary bg-gradient"
        style="background-color: rgba(var(--bs-primary-rgb), 0.5);">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">PartHub</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('parts') ? 'active' : '' }}"
                            href="{{ route('parts') }}">Parts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('boms') ? 'active' : '' }}"
                            href="{{ route('boms') }}">BOMs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('locations') ? 'active' : '' }}"
                            href="{{ route('locations') }}">Storage Locations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('categories') ? 'active' : '' }}"
                            href="{{ route('categories') }}">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('suppliers') ? 'active' : '' }}"
                            href="{{ route('suppliers') }}">Suppliers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('footprints') ? 'active' : '' }}"
                            href="{{ route('footprints') }}">Footprints</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        @unless (Auth::check())
                        <li>
                            <a class="nav-link {{ request()->routeIs('whatis') ? 'active' : '' }}"
                                href="{{ route('whatis') }}">What is PartHub?</a>
                        </li>
                        <li>
                            <a class="nav-link {{ request()->routeIs('pricing') ? 'active' : '' }}"
                                href="{{ route('pricing') }}">Pricing</a>
                        </li>
                        <li>
                            <a class="nav-link {{ request()->routeIs('signup') ? 'active' : '' }}"
                                href="{{ route('signup') }}">Sign up</a>
                        </li>
                        @endunless
                        <li>
                            <a class="nav-link dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown"><i class="fas fa-user"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end text-end w-auto px-2" style="min-width: 0;">
                                @if (Auth::check())
                                    <!-- Logged in -->
                                    <li> {{ $user_name }}</li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="nav-link" href="{{ route('dashboard') }}">Profile</a></li>
                                    <li>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <div class="d-grid justify-content-md-end">
                                                <button type="submit" class="nav-link" style="background: none; border: none;">Log
                                                    Out</button>
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
                </form>
            </div>
        </div>
    </nav>
</div>
