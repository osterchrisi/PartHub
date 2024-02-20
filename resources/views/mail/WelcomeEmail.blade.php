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
        <h1>Thank you for chosing PartHub, {{ $name }}!</h1>
        <h4>We're glad to have you onboard 🚀 </h4>
        <p>
            Your user account has been created and you can start adding parts right away.<br>
            If you have any questions or feedback don't hesitate to contact us at: <a href="mailto:hello@parthub.online">hello@parthub.online</a><br>
            <br>
            All the best,<br>
            the PartHub team from Berlin<br><br>
            <img src="{{ env('APP_FAVICON') }}" alt="PartHub Logo"></img>
        </p>
    </div>
</body>

</html>
