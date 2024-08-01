<x-email.layout>
    @slot('header')
        Welcome to PartHub, {{ $name }}!
    @endslot

    <h4>We're glad to have you onboard 🚀</h4>
    <p class="lead">Your user account has been created and you can start adding parts right away.</p>
    <p class="lead">If you have any questions or feedback, don't hesitate to contact us at: <a href="mailto:hello@parthub.online">hello@parthub.online</a></p>
    <hr>
    <p class="lead">PartHub is still in beta. It is currently free and open-source. We are actively developing new features every week. If you are looking to contribute to our project, you can do so via <a href="https://github.com/osterchrisi/PartHub" target="_blank">GitHub</a>.</p>
    <p class="lead">We are working on ways to also give financial support in order to increase the speed of development.</p>
    <hr>
    <p class="lead">Thanks again and all the best,<br>
        The PartHub Team from Berlin</p>
    <img src="{{ env('APP_FAVICON') }}" alt="PartHub Logo" style="width: 50px; height: 50px;">
</x-email.layout>
