{{-- Parent Template --}}
@extends('centered-layout')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center text-center bg-light bg-gradient rounded p-5 mb-5">
            <div class="col-md-8 text-left">
                <h1 class="display-1 mb-4" id="whatis-headline">What is PartHub?</h1>
                <h4 class="mb-3"></h4>
                <h1 class="mb-4">TLDR; I organize parts</h1>
                <p class="lead">I am an Inventory and BOM management software. I am here to help you keep your workplace
                    tidy. I take care of the boring stock keeping and you can focus on the creative part of your work.</p>
            </div>
        </div>

        <div class="row justify-content-center text-end bg-secondary bg-gradient text-white rounded p-5 mb-5">
            <div class="col-md-8 text-right">
                <h1 class="display-1 mb-4" id="whatis-headline">How can PartHub help me?</h1>
                <p class="lead">I make tracking stock fun. It's like a zombie shooter video game. Only that you use a
                    barcode scanner as a gun and shoot labels instead of zombies. (Also works without a barcode scanner)</p>
            </div>
        </div>

        <div class="row justify-content-center text-start bg-light bg-gradient rounded p-5 mb-5">
            <div class="col-md-8 text-left">
                <h1 class="display-1 mb-4" id="whatis-headline">Is PartHub for me?</h1>
                <p class="lead">Yes! If you buy parts for your projects and they come in small baggies, I am here to make
                    your life significantly easier!</p>
                <p class="lead">I currently work best with electronic parts but nothing keeps you from tracking any type
                    of parts. You can personalize me to work best for your needs.</p>
            </div>
        </div>

        <div class="row justify-content-center text-end bg-secondary bg-gradient text-white rounded p-5 mb-5">
            <div class="col-md-8 text-right">
                <h1 class="display-1 mb-4" id="whatis-headline">Who is behind PartHub?</h1>
                <p class="lead">PartHub was created by Christian Zollner, the founder of KOMA Elektronik. He needed a
                    software to help him keep production and development smooth but nothing
                    suited his needs - so he programmed the software himself!</p>
                <img src="app-related\Chrisi_und_Kaja_square.webp" class="img_fluid w-50" alt="Christian and his dog Kaja">
                <p class="lead">Christian and his dog Kaja</p>
            </div>
        </div>

        <div class="row justify-content-center text-start bg-light bg-gradient rounded p-5 mb-5">
            <div class="col-md-8 text-left">
                <h1 class="display-1 mb-4" id="whatis-headline">Features</h1>
                <p class="lead">
                <ul>
                    <li class="lead"> Keep an up-to-date inventory of your parts easily
                    <li class="lead"> Multiple storage locations per part, move stock between locations
                    <li class="lead"> Manage suppliers, footprints, units and BOMs
                    <li class="lead"> Automatic part history - so you know where your stock went
                    <li class="lead"> <em>Assemble</em> BOMs when you build projects and stock gets deducted automagically
                    <li class="lead"> Set notification thresholds for stock levels per part
                    <li class="lead"> Upload images for each part
                </ul>
                </p>
            </div>
        </div>
        <div class="row justify-content-center text-start bg-secondary bg-gradient text-white rounded p-5 mb-5">
            <div class="col-md-8 text-left">
                <h1 class="display-1 mb-4" id="whatis-headline">Future development</h1>
                <p class="lead">PartHub is constantly being improved, so if you get an account today you will
                    automatically benefit from these future developments.
                <ul>
                    <li class="lead"> Barcode scanner functionality
                    <li class="lead"> Add prices per supplier
                    <li class="lead"> Image grid part overview
                    <li class="lead"> Modify BOMs online
                    <li class="lead"> Price lookup via distributor API access
                    <li class="lead"> Automatic fillout of part details via distributor API access
                    <li class="lead"> Teams / Multi user support
                </ul>
                </p>
            </div>
        </div>
    </div>
@endsection

@section('modals and menus')
    @include('components.modals.userStuffModal')
@endsection
