{{-- Parent Template --}}
@extends('centered-layout')

@section('content')
    <div class="container my-5">
        <!-- Hero Section -->
        <div class="row justify-content-center text-center hero-section rounded-3 mb-5"
            style="background-image: url('app-related/hero-background.webp'); background-size: cover; background-position: center;">
            <div class="col-md-8">
                <h1 class="display-1 mb-4">Welcome to PartHub</h1>
                <p class="lead mb-4 bg-dark text-white p-2 rounded d-inline-block p-hero">Your Ultimate Inventory and BOM
                    Management Solution</p><br>
                <a href="{{ route('demo.login') }}" class="btn btn-lg btn-light me-2">Explore Demo</a>
                <a href="{{ route('signup') }}" class="btn btn-lg btn-light ml-2">Sign Up Now</a>
            </div>
        </div>

        <!-- What is PartHub -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <div class="card card-whatis shadow-sm rounded">
                    <div class="card-body text-center">
                        {{-- <div class="card-body text-center bg-primary bg-gradient text-white"> --}}

                        <h2 class="display-4 mb-4">Start with a clean slate</h2>
                        <img src="app-related/image1.webp" class="img-fluid mb-4"
                            alt="Person organizing parts in a workshop">
                        <p class="lead">You work best in a tidy environment. PartHub is designed to let you focus on the
                            creative
                            parts of your work. It is an Inventory and BOM management software.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- How Can PartHub Help -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <div class="card card-whatis shadow-sm rounded">
                    <div class="card-body text-center bg-primary bg-gradient text-white rounded">
                        <h2 class="display-4 mb-4">Work better</h2>
                        <img src="app-related/image2.webp" class="img-fluid mb-4" alt="Person using a barcode scanner">
                        <p class="lead">Track stock like a pro. It's fun and efficient, with or without a barcode scanner.
                            Always know what you have and where it is.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Is PartHub For You? -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <div class="card card-whatis shadow-sm rounded">
                    <div class="card-body text-center">
                        <h2 class="display-4 mb-4">Creativity starts here</h2>
                        <img src="app-related/image3.webp" class="img-fluid mb-4" alt="Close-up of electronic components">
                        <p class="lead">If you handle small parts for your projects, PartHub is here to simplify your
                            life. Ideal for electronic parts but customizable for any type of parts.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="row justify-content-center mb-5" id="features">
            <div class="col-md-8">
                <div class="card card-whatis text-center bg-primary bg-gradient text-white shadow-sm rounded">
                    <div class="card-body text-center">
                        <h2 class="display-4 mb-4">Features</h2>
                        {{-- <ul class="list-unstyled lead"> --}}
                            <ul class="list-group lead text-start">
                            <li class="list-group-item active">📦 Keep an up-to-date inventory easily</li>
                            <li class="list-group-item active">🏷️ Manage multiple storage locations</li>
                            <li class="list-group-item active">📋 Handle suppliers, footprints, units, and BOMs</li>
                            <li class="list-group-item active">📝 Automatic part history tracking</li>
                            <li class="list-group-item active">⚙️ Assemble BOMs with automatic stock deduction</li>
                            <li class="list-group-item active">🔔 Set notification thresholds</li>
                            <li class="list-group-item active">📷 Upload images for each part</li>
                        </ul>
                        <a href="{{ route('demo.login') }}" class="btn btn-lg btn-outline-light ml-2 cta-btn mt-5">Try It For Yourself</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Who is Behind PartHub -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <div class="card card-whatis shadow-sm rounded">
                    <div class="card-body text-center bg-gradient text-white" style="background-color: #6c757d;">
                        <h2 class="display-4 mb-4">Who is Behind PartHub?</h2>
                        <p class="lead">PartHub was created by <a href="https://christianzollner.com" target="_blank"
                                class="text-white font-weight-bold">Christian Zollner</a>, founder of <a
                                href="https://koma-elektronik.com" target="_blank" class="text-white font-weight-bold">KOMA
                                Elektronik</a>. Christian programmed this software because no inventory tracking software of
                            this kind existed for his company.</p>
                        <img src="app-related/Chrisi_und_Kaja_square.webp" class="img-fluid rounded-circle w-25 mb-3"
                            alt="Christian and his dog Kaja">
                        <p class="lead fw-lighter">Christian and his dog Kaja</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Future Development -->
        {{-- <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <div class="card shadow-sm rounded">
                    <div class="card-body text-center bg-gradient text-white" style="background-color: #6c757d;">
                        <h2 class="display-4 mb-4">Future Development</h2>
                        <p class="lead">Get an account today and benefit from continuous improvements:</p>
                        <ul class="list-unstyled lead">
                            <li>🔍 Barcode scanner functionality</li>
                            <li>💲 Add prices per supplier</li>
                            <li>🖼️ Image grid part overview</li>
                            <li>🔧 Modify BOMs online</li>
                            <li>📦 Price lookup via distributor API</li>
                            <li>🔍 Automatic part details via API</li>
                            <li>👥 Teams/Multi-user support</li>
                        </ul>
                        <a href="{{ route('signup') }}" class="btn btn-lg btn-light mt-3 cta-btn">Join Us Today</a>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Call to Action -->
        <div class="row justify-content-center mb-5" id="sign-up">
            <div class="col-md-8">
                <div class="card card-whatis shadow-sm rounded">
                    <div class="card-body text-center bg-primary bg-gradient text-white">
                        <h2 class="display-4 mb-4">Ready to Get Started?</h2>
                        <p class="lead">Sign up today and start organizing your parts like never before!</p>
                        <a href="{{ route('signup') }}" class="btn btn-lg btn-light cta-btn me-3">Sign Up Now</a>
                        <a href="{{ route('demo.login') }}" class="btn btn-lg btn-outline-light ml-2 cta-btn">Try Demo</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modals and menus')
    @include('components.modals.userStuffModal')
@endsection
