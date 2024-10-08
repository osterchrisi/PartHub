<x-email.layout>
    @slot('header')
        Welcome to PartHub, {{ $name }}!
    @endslot

    <h4>We're glad to have you onboard 🚀</h4>
    <p class="lead">Your user account has been created and you can start adding parts right away.</p>
    <p class="lead">If you have any questions or feedback, don't hesitate to contact us at: <a href="mailto:hello@parthub.online">hello@parthub.online</a></p>
    <hr>
    <p class="lead">PartHub is currently free and open-source. We are continously developing new features. If you are looking to contribute to our project, you can do so via <a href="https://github.com/osterchrisi/PartHub" target="_blank">GitHub</a>.</p>
    <hr>
    <p class="lead">Thanks again and all the best,<br>
        The PartHub Team from Berlin</p>
    <img src="{{ asset(config('app.logo')) }}" alt="PartHub Logo" style="width: 50px; height: 50px;">
</x-email.layout>
