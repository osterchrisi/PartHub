@extends('centered-layout')

@section('content')
    <div class="container-fluid my-5">
        <!-- Hero Section -->
        <x-hero-card divExtraClass="justify-content-center text-center" backgroundImage="app-related/adobe_stock_background2.jpeg"
            title="Save Time and Stay Organized with PartHub" subtitle="Eliminate constant part counts, prevent over-ordering, and ensure accurate stock levels effortlessly"
            firstButtonRoute="{{ route('demo.login') }}" secondButtonRoute="{{ route('signup') }}"
            firstButtonText="Explore Demo" secondButtonText="Sign Up Now"/>

        <!-- Left-div -->
        <x-left-div title="Clean desk, clear mind"
            imageSrc="app-related/image1.webp" 
            imageAlt="Person organizing parts in a workshop"
            content="Effortlessly track your parts: Maintain accurate stock levels without manual counts. PartHub makes it easy to keep track of your inventory."
            bgClass="card-light-bg signup-gradient-background text-white" />

        <!-- Right-div -->
        <x-right-div title="Stay in Control, Work Smarter"
            imageSrc="app-related/image2.webp" 
            imageAlt="Person using a barcode scanner"
            content="Track stock like a pro, with or without a barcode scanner. Always know exactly what you have and where it is—no more guesswork, no more lost parts."
            bgClass="pricing-gradient-background card-primary-bg text-white" />

        <!-- Left-div -->
        <x-left-div title="More Making, Less Managing"
            imageSrc="app-related/image3.webp" 
            imageAlt="Close-up of electronic components"
            content="Whether you're prototyping, building, or repairing, keeping track of small parts can be a nightmare. PartHub takes care of the logistics so you can focus on making."
            bgClass="card-light-bg signup-gradient-background text-white" />

        <!-- Feature List Section -->
        <x-left-div 
            imageSrc="app-related/feature-images/KeepTrack.png" 
            imageAlt="Feature 1"
            title="📦 Keep an up-to-date inventory easily"
            content="Forget spreadsheets and messy stock counts. PartHub automatically keeps your inventory accurate and accessible at all times."
            bgClass="pricing-gradient-background card-primary-bg text-white" />

        <x-right-div 
            imageSrc="app-related/feature-images/MultipleStorage.png" 
            imageAlt="Feature 2"
            title="🏷️ Manage multiple storage locations"
            content="Track parts across different storage locations and keep everything organized with ease. Know exactly where every part is stored, whether it’s in a warehouse, a workshop, or a drawer in your home lab."
            bgClass="card-light-bg signup-gradient-background text-white" /> />

        <x-left-div 
            imageSrc="app-related/feature-images/Suppliers.png" 
            imageAlt="Feature 3"
            title="📋 Handle suppliers, footprints, datasheets, and BOMs"
            content="Track suppliers, datasheets, footprints, alternatives and projects all in one place — so you always have the right part from the right source."
            bgClass="bg-gradient card-primary-bg text-white" />

        <x-right-div 
            imageSrc="app-related/feature-images/StockHistory.png" 
            imageAlt="Feature 4"
            title="📝 Automatic part history tracking"
            content="Every movement, every change—automatically logged, so you always have a complete history of your inventory."
            bgClass="card-light-bg signup-gradient-background text-white" /> />

        <x-left-div 
            imageSrc="app-related/feature-images/AssembleBom.png" 
            imageAlt="Feature 5"
            title="⚙️ Assemble BOMs with automatic stock deduction"
            content="Build your projects faster with BOM assembly that automagically adjusts your stock levels. No more miscounted parts or unexpected shortages."
            bgClass="bg-gradient card-primary-bg text-white" />

        <x-right-div 
            imageSrc="app-related/feature-images/StockLevelNotification.png" 
            imageAlt="Feature 6"
            title="🔔 Set notification thresholds"
            content="Set low-stock alerts so you’re never caught off guard. Keep your production running smoothly without last-minute orders."
            bgClass="card-light-bg signup-gradient-background text-white" />

        <x-left-div 
            imageSrc="app-related/feature-images/Images.png" 
            imageAlt="Feature 7"
            title="📷 Upload images for each part"
            content="Attach images to every part for quick reference, reducing confusion and speeding up your workflow."
            bgClass="bg-gradient card-primary-bg text-white" />

        <!-- About PartHub -->
        <x-right-div title="Who is Behind PartHub?"
            imageSrc="app-related/Chrisi_und_Kaja_square.webp" 
            imageAlt="Christian and his dog Kaja"
            content='PartHub was built by <a href="https://christianzollner.com" target="_blank" class="text-white">Christian Zollner</a>, founder of <a href="https://koma-elektronik.com" target="_blank" class="text-white">KOMA Elektronik</a>, because no inventory tool existed that truly fit his needs. So he made one. Now, you can use it too. . <br><span class="text-muted fs-6"> (His dog Kaja helped a little too)</span'
            imageClass="rounded-circle w-50"
            bgClass="card-light-bg signup-gradient-background text-white" />

        <!-- Call to Action -->
        <div class="row mb-5" id="whatis-call-to-action-div">
            <div class="col-12 bg-primary bg-gradient text-white text-center p-5 rounded">
                <h2 class="display-3 mb-4">Ready to Get Started?</h2>
                <p class="lead fs-2">Sign up today and <span class="fw-bold">stop wasting time on inventory - get back to building!</span></p>
                <a href="{{ route('signup') }}" class="btn btn-lg gradient-hero-button btn-light extra-shadow mt-4 fs-1">Sign Up Now</a>
            </div>
        </div>
    </div>
@endsection

@section('modals and menus')
@endsection
