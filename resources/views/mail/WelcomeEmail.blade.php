<x-email.layout>
    @slot('header')
        Welcome to PartHub, {{ $name }}!
    @endslot

    <h4>We're excited to have you onboard! 🚀</h4>
    <p class="lead">Your account is ready, and you can start organizing your parts right away.</p>

    @if ($planType === 'maker')
        <p class="lead">To get started, simply <a href="{{ route('login') }}">log in</a> and start adding your parts.
        </p>
    @else
        <p class="lead">To get the most out of PartHub, we highly recommend checking out our <strong>Maker
                plan</strong>.
            It offers advanced features to streamline your inventory management — currently at a crazy discount for
            <strong>only €2,99/month!</strong> 🤯
        </p>
        <p class="lead" style="text-align: center;">
            <a href="{{ route('signup') }}#register-pricing"
                style="display: inline-block; padding: 10px 15px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px;">
                Explore the Maker Plan
            </a>
        </p>
    @endif

    <hr>

    <p class="lead">If you have any questions or feedback, feel free to reach out:
        <a href="mailto:hello@parthub.online">hello@parthub.online</a>
    </p>

    <hr>

    <p class="lead">Thanks again and happy building!<br>
        — The PartHub Team from Berlin</p>

    <img src="{{ asset(config('app.logo')) }}" alt="PartHub Logo" style="width: 50px; height: 50px;">
</x-email.layout>
