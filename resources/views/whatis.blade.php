@extends('centered-layout')

@section('content')
    <div class="container my-5">
        <!-- Hero Section -->
        <x-hero-card divExtraClass="justify-content-center text-center" backgroundImage="app-related/hero-background.webp"
            title="Welcome to PartHub" subtitle="Your Ultimate Inventory and BOM Management Solution"
            demoRoute="{{ route('demo.login') }}" signupRoute="{{ route('signup') }}" />

        <!-- Left-div -->
        <x-left-div title="Start with a clean slate"
            imageSrc="app-related/image1.webp" 
            imageAlt="Person organizing parts in a workshop"
            content="You work best in a tidy environment. PartHub is designed to let you focus on the creative parts of your work. It is an Inventory and BOM management software."
            bgClass="card-light-bg" />

        <!-- Right-div -->
        <x-right-div title="Work better"
            imageSrc="app-related/image2.webp" 
            imageAlt="Person using a barcode scanner"
            content="Track stock like a pro. It's fun and efficient, with or without a barcode scanner. Always know what you have and where it is."
            bgClass="bg-gradient card-primary-bg text-white" />

        <!-- Left-div -->
        <x-left-div title="Creativity starts here"
            imageSrc="app-related/image3.webp" 
            imageAlt="Close-up of electronic components"
            content="If you handle small parts for your projects, PartHub is here to simplify your life. Ideal for electronic parts but customizable for any type of parts."
            bgClass="bg-light" />

        <!-- Feature List Section -->
        <x-feature-left 
            imageSrc="app-related/feature-images/KeepTrack.png" 
            imageAlt="Feature 1"
            tagline="📦 Keep an up-to-date inventory easily"
            description="Effortlessly manage your inventory and always stay up to date with the latest stock levels." />

        <x-feature-right 
            imageSrc="app-related/feature-images/MultipleStorage.png" 
            imageAlt="Feature 2"
            tagline="🏷️ Manage multiple storage locations"
            description="Track parts across different storage locations and keep everything organized with ease." />

        <x-feature-left 
            imageSrc="app-related/feature-images/Suppliers.png" 
            imageAlt="Feature 3"
            tagline="📋 Handle suppliers, footprints, datasheets, and BOMs"
            description="Comprehensive supplier, footprint, and BOM management to streamline your operations." />

        <x-feature-right 
            imageSrc="app-related/feature-images/StockHistory.png" 
            imageAlt="Feature 4"
            tagline="📝 Automatic part history tracking"
            description="Track the history of each part automatically, giving you a complete audit trail." />

        <x-feature-left 
            imageSrc="app-related/feature-images/AssembleBom.png" 
            imageAlt="Feature 5"
            tagline="⚙️ Assemble BOMs with automatic stock deduction"
            description="Build and assemble BOMs quickly with automatic stock deductions for efficiency." />

        <x-feature-right 
            imageSrc="app-related/feature-images/StockLevelNotification.png" 
            imageAlt="Feature 6"
            tagline="🔔 Set notification thresholds"
            description="Set notification thresholds to stay informed about low stock and important updates." />

        <x-feature-left 
            imageSrc="app-related/feature-images/Images.png" 
            imageAlt="Feature 7"
            tagline="📷 Upload images for each part"
            description="Visualize your parts inventory with images for easy identification and management." />

        <!-- About PartHub -->
        <x-right-div title="Who is Behind PartHub?"
            imageSrc="app-related/Chrisi_und_Kaja_square.webp" 
            imageAlt="Christian and his dog Kaja"
            content='PartHub was created by <a href="https://christianzollner.com" target="_blank" class="text-white">Christian Zollner</a>, founder of <a href="https://koma-elektronik.com" target="_blank" class="text-white">KOMA Elektronik</a>. Christian programmed this software because no inventory tracking software of this kind existed for his company.'
            imageClass="rounded-circle w-50"
            bgClass="bg-secondary bg-gradient text-white" />

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
