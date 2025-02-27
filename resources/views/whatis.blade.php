@extends('centered-layout')

@section('content')
    <div class="container-fluid my-5">
        <!-- Hero Section -->
        <x-hero-card divExtraClass="justify-content-center text-center" backgroundImage="app-related/adobe_stock_background2.jpeg"
            title="PartHub" subtitle="Your Ultimate Inventory Tracking Solution"
            firstButtonRoute="{{ route('demo.login') }}" secondButtonRoute="{{ route('signup') }}"
            firstButtonText="Explore Demo" secondButtonText="Sign Up Now"/>

        <!-- Left-div -->
        <x-left-div title="Start with a clean slate"
            imageSrc="app-related/image1.webp" 
            imageAlt="Person organizing parts in a workshop"
            content="You work best in a tidy environment. PartHub is designed to let you focus on the creative parts of your work. It is an Inventory and BOM management software."
            bgClass="card-light-bg signup-gradient-background" />

        <!-- Right-div -->
        <x-right-div title="Work better"
            imageSrc="app-related/image2.webp" 
            imageAlt="Person using a barcode scanner"
            content="Track stock like a pro. It's fun and efficient, with or without a barcode scanner. Always know what you have and where it is."
            bgClass="pricing-gradient-background card-primary-bg text-white" />

        <!-- Left-div -->
        <x-left-div title="Creativity starts here"
            imageSrc="app-related/image3.webp" 
            imageAlt="Close-up of electronic components"
            content="If you handle small parts for your projects, PartHub is here to simplify your life. Ideal for electronic parts but customizable for any type of inventory."
            bgClass="card-light-bg signup-gradient-background" />

        <!-- Feature List Section -->
        <x-left-div 
            imageSrc="app-related/feature-images/KeepTrack.png" 
            imageAlt="Feature 1"
            title="📦 Keep an up-to-date inventory easily"
            content="Effortlessly manage your inventory and always stay up to date with the latest stock levels."
            bgClass="pricing-gradient-background card-primary-bg text-white"/>

        <x-right-div 
            imageSrc="app-related/feature-images/MultipleStorage.png" 
            imageAlt="Feature 2"
            title="🏷️ Manage multiple storage locations"
            content="Track parts across different storage locations and keep everything organized with ease." />

        <x-left-div 
            imageSrc="app-related/feature-images/Suppliers.png" 
            imageAlt="Feature 3"
            title="📋 Handle suppliers, footprints, datasheets, and BOMs"
            content="Comprehensive supplier, footprint, and BOM management to streamline your operations."
            bgClass="bg-gradient card-primary-bg text-white" />

        <x-right-div 
            imageSrc="app-related/feature-images/StockHistory.png" 
            imageAlt="Feature 4"
            title="📝 Automatic part history tracking"
            content="Track the history of each part automatically, giving you a complete audit trail." />

        <x-left-div 
            imageSrc="app-related/feature-images/AssembleBom.png" 
            imageAlt="Feature 5"
            title="⚙️ Assemble BOMs with automatic stock deduction"
            content="Build and assemble BOMs quickly with automatic stock deductions for efficiency."
            bgClass="bg-gradient card-primary-bg text-white" />

        <x-right-div 
            imageSrc="app-related/feature-images/StockLevelNotification.png" 
            imageAlt="Feature 6"
            title="🔔 Set notification thresholds"
            content="Set notification thresholds to stay informed about low stock and important updates." />

        <x-left-div 
            imageSrc="app-related/feature-images/Images.png" 
            imageAlt="Feature 7"
            title="📷 Upload images for each part"
            content="Visualize your parts inventory with images for easy identification and management."
            bgClass="bg-gradient card-primary-bg text-white" />

        <!-- About PartHub -->
        <x-right-div title="Who is Behind PartHub?"
            imageSrc="app-related/Chrisi_und_Kaja_square.webp" 
            imageAlt="Christian and his dog Kaja"
            content='PartHub was created by <a href="https://christianzollner.com" target="_blank" class="text-white">Christian Zollner</a>, founder of <a href="https://koma-elektronik.com" target="_blank" class="text-white">KOMA Elektronik</a>. Christian programmed this software because no inventory tracking software of this kind existed for his company.'
            imageClass="rounded-circle w-50"
            bgClass="bg-secondary bg-gradient text-white" />

        <!-- Call to Action -->
        <div class="row mb-5" id="whatis-call-to-action-div">
            <div class="col-12 bg-primary bg-gradient text-white text-center p-5 rounded">
                <h2 class="display-3 mb-4">Ready to Get Started?</h2>
                <p class="lead fs-2">Sign up today and start organizing your parts like never before!</p>
                <a href="{{ route('signup') }}" class="btn btn-lg gradient-hero-button btn-light extra-shadow mt-4 fs-1">Sign Up Now</a>
            </div>
        </div>
    </div>
@endsection

@section('modals and menus')
@endsection
