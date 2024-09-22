@extends('centered-layout')

@section('content')
    <div class="container my-5">
        <!-- Hero Section -->
        <x-hero-card divExtraClass="justify-content-center text-center" backgroundImage="app-related/hero-background.webp"
            title="Welcome to PartHub" subtitle="Your Ultimate Inventory and BOM Management Solution"
            demoRoute="{{ route('demo.login') }}" signupRoute="{{ route('signup') }}" />

        <!-- Alternating Full-Width Cards -->
        <div class="row mb-5">
            <div class="col-12 card-light-bg">
                <div class="row py-5 align-items-center">
                    <!-- Left-aligned image -->
                    <div class="col-lg-6 text-center">
                        <img src="app-related/image1.webp" alt="Person organizing parts in a workshop" class="img-fluid rounded w-75">
                    </div>
                    <div class="col-lg-6">
                        <h2 class="display-4 my-4 mx-5">Start with a clean slate</h2>
                        <p class="lead">You work best in a tidy environment. PartHub is designed to let you focus on the creative parts of your work. It is an Inventory and BOM management software.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12bg-gradient card-primary-bg text-white">
                <div class="row py-5 align-items-center">
                    <!-- Right-aligned image -->
                    <div class="col-lg-6 order-lg-2 text-center">
                        <img src="app-related/image2.webp" alt="Person using a barcode scanner" class="img-fluid rounded w-75">
                    </div>
                    <div class="col-lg-6 order-lg-1">
                        <h2 class="display-4 my-4 mx-5">Work better</h2>
                        <p class="lead mx-5">Track stock like a pro. It's fun and efficient, with or without a barcode scanner. Always know what you have and where it is.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12 bg-light">
                <div class="row py-5 align-items-center">
                    <!-- Left-aligned image -->
                    <div class="col-lg-6 text-center">
                        <img src="app-related/image3.webp" alt="Close-up of electronic components" class="img-fluid rounded w-75">
                    </div>
                    <div class="col-lg-6">
                        <h2 class="display-4 my-4 mx-5">Creativity starts here</h2>
                        <p class="lead">If you handle small parts for your projects, PartHub is here to simplify your life. Ideal for electronic parts but customizable for any type of parts.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature List Full-Width -->
        <div class="row mb-5">
            <div class="col-12 bg-primary bg-gradient text-white text-center p-5 rounded shadow-sm">
                <h2 class="display-4 mb-4">Features</h2>
                <ul class="list-group list-group-flush lead">
                    @foreach ([
                        '📦 Keep an up-to-date inventory easily',
                        '🏷️ Manage multiple storage locations',
                        '📋 Handle suppliers, footprints, units, and BOMs',
                        '📝 Automatic part history tracking',
                        '⚙️ Assemble BOMs with automatic stock deduction',
                        '🔔 Set notification thresholds',
                        '📷 Upload images for each part'
                    ] as $feature)
                        <li class="list-group-item bg-primary text-white">{{ $feature }}</li>
                    @endforeach
                </ul>
                <a href="{{ route('demo.login') }}" class="btn btn-lg btn-light mt-4">Try It For Yourself</a>
            </div>
        </div>

        <!-- About PartHub -->
        <div class="row mb-5">
            <div class="col-12 bg-secondary bg-gradient text-white">
                <div class="row py-5 align-items-center">
                    <!-- Left-aligned image -->
                    <div class="col-lg-6 text-center">
                        <img src="app-related/Chrisi_und_Kaja_square.webp" alt="Christian and his dog Kaja" class="img-fluid rounded-circle w-50">
                    </div>
                    <div class="col-lg-6">
                        <h2 class="display-4 mb-4">Who is Behind PartHub?</h2>
                        <p class="lead">
                            PartHub was created by <a href="https://christianzollner.com" target="_blank" class="text-white">Christian Zollner</a>,
                            founder of <a href="https://koma-elektronik.com" target="_blank" class="text-white">KOMA Elektronik</a>.
                            Christian programmed this software because no inventory tracking software of this kind existed for his company.
                        </p>
                        <p class="lead fw-lighter">Christian and his dog Kaja</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="row mb-5">
            <div class="col-12 bg-primary bg-gradient text-white text-center p-5 rounded">
                <h2 class="display-4 mb-4">Ready to Get Started?</h2>
                <p class="lead">Sign up today and start organizing your parts like never before!</p>
                <a href="{{ route('signup') }}" class="btn btn-lg btn-light mt-4">Sign Up Now</a>
            </div>
        </div>
    </div>
@endsection

@section('modals and menus')
    @include('components.modals.userStuffModal')
@endsection
