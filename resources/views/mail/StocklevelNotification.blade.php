<x-email.layout>
    @slot('header')
        Hey, {{ $name }}!
    @endslot

    <h4>There are some important stock level changes</h4>
    <p class="lead">
        Part: {{ $stock_level[3] }}<br>
        ID: {{ $stock_level[0] }}<br>
        Location: {{ $stock_level[2] }}<br>
        New Quantity: {{ $stock_level[1] }}<br>
    </p>
    <hr>
    <p class="lead">
        All the best,<br>
        The PartHub team from Berlin<br>
        <img src="{{ env('APP_FAVICON') }}" alt="PartHub Logo" style="width: 50px; height: 50px;">
    </p>
</x-email.layout>
