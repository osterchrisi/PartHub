<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Notification' }}</title>
    <style>
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-size: 1rem;
            background-color: #f8f9fa;
            color: #212529;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 1rem;
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: rgba(32, 62, 105, 1);
            color: #ffffff;
            padding: 1rem;
            border-radius: 0.5rem 0.5rem 0 0;
            text-align: center;
        }
        .content {
            padding: 1rem;
        }
        h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            text-align: center;
        }
        h4 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        p {
            font-size: 1rem;
            margin-bottom: 1rem;
        }
        a {
            color: #0d6efd;
            text-decoration: none;
        }
        .button {
            display: inline-block;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            color: #ffffff;
            background-color: #0d6efd;
            border-radius: 0.25rem;
            text-decoration: none;
        }
        .lead {
            font-size: 1.25rem;
            font-weight: 300;
            line-height: 1.5;
        }
        .footer {
            background-color: #f8f9fa;
            color: #6c757d;
            padding: 1rem;
            border-radius: 0 0 0.5rem 0.5rem;
            text-align: center;
            font-size: 0.875rem;
        }
        .footer a {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $header }}</h1>
        </div>
        <div class="content">
            {{ $slot }}
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p><a href="{{ url('/') }}">Visit PartHub</a></p>
        </div>
    </div>
</body>
</html>
