@extends('centered-layout')

@section('content')
    <div class="container my-5">
        <!-- Sign Up Hero Section -->
        <div class="row hero-section rounded-3 mb-5 justify-content-center text-center"
            style="background-image: url('app-related/hero-background.webp'); background-size: cover; background-position: center;">
            <div class="col-md-8 text-white">
                <h1 class="display-1 mb-4">Get Started with PartHub</h1>
                <p class="lead mb-4 bg-dark text-white p-2 rounded d-inline-block p-hero">Choose the plan that suits you best and start organizing your parts with ease!</p><br>
                <a href="#pricing" class="btn btn-lg btn-light me-2">See Pricing</a>
            </div>
        </div>

        <!-- Pricing Section -->
        <div id="pricing" class="container my-5 text-center">
            <h2 class="display-4 mb-4">Pricing Plans</h2>
            <p class="lead">Pick the plan that best suits your needs.</p>
            
            <div class="row justify-content-center">
                <!-- Free Tier -->
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title">Free</h3>
                            <p class="lead text-muted">Perfect for small projects</p>
                            <h4 class="pricing">$0/month</h4>
                            <ul class="list-unstyled my-4">
                                <li>🔹 Up to 100 parts</li>
                                <li>🔹 1 storage location</li>
                                <li>🔹 Basic inventory management</li>
                                <li>🔹 Community support</li>
                            </ul>
                            <a href="" class="btn btn-outline-primary btn-lg">Sign Up for Free</a>
                        </div>
                    </div>
                </div>

                <!-- Pro Tier -->
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title">Pro</h3>
                            <p class="lead text-muted">Best for growing businesses</p>
                            <h4 class="pricing">$29/month</h4>
                            <ul class="list-unstyled my-4">
                                <li>🔹 Unlimited parts</li>
                                <li>🔹 Multiple storage locations</li>
                                <li>🔹 Supplier management</li>
                                <li>🔹 Premium support</li>
                            </ul>
                            <a href="" class="btn btn-primary btn-lg">Start Pro Trial</a>
                        </div>
                    </div>
                </div>

                <!-- Enterprise Tier -->
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title">Enterprise</h3>
                            <p class="lead text-muted">For large-scale organizations</p>
                            <h4 class="pricing">Contact Us</h4>
                            <ul class="list-unstyled my-4">
                                <li>🔹 Customized solutions</li>
                                <li>🔹 Unlimited everything</li>
                                <li>🔹 Advanced BOM management</li>
                                <li>🔹 Dedicated support team</li>
                            </ul>
                            <a href="" class="btn btn-outline-primary btn-lg">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="row my-5">
            <div class="col-12 bg-primary bg-gradient text-white text-center p-5 rounded shadow">
                <h2 class="display-4 mb-4">Ready to Get Started?</h2>
                <p class="lead">Choose your plan and start organizing your inventory like never before!</p>
                <a href="#pricing" class="btn btn-lg btn-light">Choose Your Plan</a>
            </div>
        </div>
    </div>
@endsection