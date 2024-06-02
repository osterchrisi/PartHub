<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Welcome Template</title>
    <style>
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-size: 1rem;
        }
    </style>
</head>

<body>
    <div>
        <h1>Hey, {{ $name }}!</h1>
        <h4>There are some important stock level changes </h4>
        
        Part: {{$stock_level[3]}}<br>
        ID: {{$stock_level[0]}}<br>
        Location: {{$stock_level[2]}}<br>
        New Quantity: {{$stock_level[1]}}<br>

        <p>
            All the best,<br>
            the PartHub team from Berlin<br><br>
            <img src="{{ env('APP_FAVICON') }}" alt="PartHub Logo"></img>
        </p>
        <br><br>
        PS: This message will look nicer in the future :)
    </div>
</body>

</html>
