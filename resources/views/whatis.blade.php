{{-- Parent Template --}}
@extends('centered-layout')

@section('content')
    <div class="container my-5">
        <!-- Hero Section -->
        <x-hero-card backgroundImage="app-related/hero-background.webp" title="Welcome to PartHub"
            subtitle="Your Ultimate Inventory and BOM Management Solution" demoRoute="{{ route('demo.login') }}"
            signupRoute="{{ route('signup') }}" />

        <!-- What is PartHub -->
        <x-whatis-card title="Start with a clean slate" imageSrc="app-related/image1.webp"
            imageAlt="Person organizing parts in a workshop"
            content="You work best in a tidy environment. PartHub is designed to let you focus on the creative parts of your work. It is an Inventory and BOM management software."
            bgClass="" imageExtraClass="w-50" />

        <!-- How Can PartHub Help -->
        <x-whatis-card title="Work better" imageSrc="app-related/image2.webp" imageAlt="Person using a barcode scanner"
            content="Track stock like a pro. It's fun and efficient, with or without a barcode scanner. Always know what you have and where it is."
            bgClass="bg-primary bg-gradient text-white rounded" imageExtraClass="w-50" opacity />

        <!-- Is PartHub For You? -->
        <x-whatis-card title="Creativity starts here" imageSrc="app-related/image3.webp"
            imageAlt="Close-up of electronic components"
            content="If you handle small parts for your projects, PartHub is here to simplify your life. Ideal for electronic parts but customizable for any type of parts."
            bgClass="" imageExtraClass="w-50"  />

        <!-- Features -->
        <x-whatis-card title="Features" imageSrc="" imageAlt="" content=""
            bgClass="bg-primary bg-gradient text-white shadow-sm rounded" :listItems="[
                '📦 Keep an up-to-date inventory easily',
                '🏷️ Manage multiple storage locations',
                '📋 Handle suppliers, footprints, units, and BOMs',
                '📝 Automatic part history tracking',
                '⚙️ Assemble BOMs with automatic stock deduction',
                '🔔 Set notification thresholds',
                '📷 Upload images for each part',
            ]" ctaText="Try It For Yourself"
            ctaLink="{{ route('demo.login') }}"/>

        <!-- Who is Behind PartHub -->
        <x-whatis-card title="Who is Behind PartHub?" imageSrc="app-related/Chrisi_und_Kaja_square.webp"
            imageAlt="Christian and his dog Kaja" bgClass="bg-gradient bg-secondray" imageExtraClass="rounded-circle w-25">
            <p class="lead">
                PartHub was created by <a href="https://christianzollner.com" target="_blank">Christian Zollner</a>,
                founder of <a href="https://koma-elektronik.com" target="_blank">KOMA
                    Elektronik</a>.
                Christian programmed this software because no inventory tracking software of this kind existed for his
                company.
            </p>
            <p class="lead fw-lighter">Christian and his dog Kaja</p>
        </x-whatis-card>

        <!-- Call to Action -->
        <x-whatis-card title="Ready to Get Started?" imageSrc="" imageAlt=""
            content="Sign up today and start organizing your parts like never before!"
            bgClass="bg-primary bg-gradient text-white" ctaText="Sign Up Now" ctaLink="{{ route('signup') }}" />
    </div>
@endsection


@section('modals and menus')
    @include('components.modals.userStuffModal')
@endsection
