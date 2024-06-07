{{-- Parent Template --}}
@extends('centered-layout')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center text-center bg-light bg-gradient rounded p-5 mb-5">
            <div class="col-md-8 text-left">
                <h1 class="display-1 mb-4" id="whatis-headline">What is PartHub?</h1>
                <h4 class="mb-3"></h4>
                <h1 class="mb-4">TLDR; I organize parts</h1>
                <p class="lead">I am an Inventory and BOM management software. I am here to help you keep your workplace tidy. I take care of the boring stock keeping and you can focus on the creative part of your work.</p>
            </div>
        </div>

        <div class="row justify-content-center text-end bg-secondary bg-gradient text-white rounded p-5 mb-5">
            <div class="col-md-8 text-right">
                <h1 class="display-1 mb-4" id="whatis-headline">How can PartHub help me?</h1>
                <p class="lead">I make tracking stock fun. It's like a zombie shooter video game. Only that you use a barcode scanner as a gun and shoot labels instead of zombies. (Also works without a barcode scanner)</p>
            </div>
        </div>

        <div class="row justify-content-center text-start bg-light bg-gradient rounded p-5 mb-5">
            <div class="col-md-8 text-left">
                <h1 class="display-1 mb-4" id="whatis-headline">Is PartHub for me?</h1>
                <p class="lead">Yes! If you buy electronic parts for your projects and they come in small baggies with labels, I am here to make your life significantly easier!</p>
            </div>
        </div>

        <div class="row justify-content-center text-end bg-secondary bg-gradient text-white rounded p-5 mb-5">
            <div class="col-md-8 text-right">
                <h1 class="display-1 mb-4" id="whatis-headline">Will PartHub always look this ugly?</h1>
                <p class="lead">No! We promise! 🙌 </p>
                <p class="lead">We take care of the functionality first and the user interface will follow!</p>
            </div>
        </div>
    </div>
@endsection

@section('modals and menus')
    @include('components.modals.userStuffModal')
@endsection
