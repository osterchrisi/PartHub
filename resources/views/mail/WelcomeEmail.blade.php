<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Welcome Template</title>
    <style>
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-size: 1rem;
            /* background-color: rgba(0, 75, 145, 0.85); */
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
            PartHub is still in beta. It is currently free and open-source.<br>
            We are actively developing new features every week. If you are looking to contribute to our project, you can do so via <a href="https://github.com/osterchrisi/PartHub" target="_blank">GitHub</a>.<br>
            We are working on ways to also give financial support in order to increase the speed of development.
            <br><br>
            Again, we would love to hear your thoughts and feedback. Just drop us a line to <a href="mailto:hello@parthub.online">hello@parthub.online</a>.
            <br><br>
            Thanks again and all the best,<br>
            the PartHub team from Berlin<br><br>
            <img src="{{ env('APP_FAVICON') }}" alt="PartHub Logo"></img>
        </p>
    </div>
</body>

</html>
