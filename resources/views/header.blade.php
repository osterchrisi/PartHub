<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords"
        content="inventory management, electronics part inventory, inventory, electronics inventory, BOMs (bills of materials), BOM, production tracking, stock keeping, supply chain management, warehouse management, parts tracking, component tracking, parts inventory, component inventory, self-hosted inventory management, cloud-based inventory management, inventory software, open-source, electronic part inventory, BOM creation, BOM execution">
    <meta name="description"
        content="Your friendly electronic parts inventory and BOM management. Simple. Free tiers and self-hosting. Aimed at small electronic makers and tinkerers">
    <title>PartHub - {{ $title }}
    </title>

    <!-- Favicons -->
    <!-- <link rel="apple-touch-icon" sizes="180x180" href="/PartHub/assets/favicon/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/PartHub/assets/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/PartHub/assets/favicon/favicon-16x16.png">
  <link rel="manifest" href="/PartHub/site.webmanifest"> -->

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- JQuery UI -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
    <!-- JQuery Touch Punch -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
    <!-- Selectize -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.bootstrap5.min.css"
        integrity="sha512-Ars0BmSwpsUJnWMw+KoUKGKunT7+T8NGK0ORRKj+HT8naZzLSIQoOSIIM3oyaJljgLxFi0xImI5oZkAWEFARSA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- JSTree -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>

    <!-- Custom Bootstrap Theme-->
    <!-- <link href="/PartHub/assets/scss/quartz-bootstrap.min.css" rel="stylesheet"> -->
    <!-- OG Bootstrap Theme-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

    <!-- Bootstrap Table -->
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.21.3/dist/bootstrap-table.min.css">
    <script src="https://unpkg.com/bootstrap-table@1.21.3/dist/bootstrap-table.min.js"></script>
    <!-- Bootstrap Table Editable -->
    <script src="https://unpkg.com/bootstrap-table@1.21.3/dist/extensions/editable/bootstrap-table-editable.js"></script>
    <!-- Bootstrap Table Resizable -->
    <link href="https://unpkg.com/jquery-resizable-columns@0.2.3/dist/jquery.resizableColumns.css" rel="stylesheet">
    <script src="https://unpkg.com/jquery-resizable-columns@0.2.3/dist/jquery.resizableColumns.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.3/dist/extensions/resizable/bootstrap-table-resizable.min.js">
    </script>
    <!-- Bootstrap Table Reorder Columns -->
    <link href="https://cdn.jsdelivr.net/gh/akottr/dragtable@master/dragtable.css" rel="stylesheet">
    {{-- @vite('resources/js/dragtable/dragtable.css') --}}
    {{-- @vite('resources/js/dragtable/jquery.dragtable.js') --}}
    <script src="https://cdn.jsdelivr.net/gh/akottr/dragtable@master/jquery.dragtable.js"></script>
    <script
        src="https://unpkg.com/bootstrap-table@1.21.3/dist/extensions/reorder-columns/bootstrap-table-reorder-columns.js">
    </script>
    <!-- Bootstrap Table Cookie to remember table state -->
    <script src="https://unpkg.com/bootstrap-table@1.21.3/dist/extensions/cookie/bootstrap-table-cookie.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/jquery-treegrid@0.3.0/css/jquery.treegrid.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery-treegrid@0.3.0/js/jquery.treegrid.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.4/dist/extensions/treegrid/bootstrap-table-treegrid.min.js">
    </script>

    <!-- Lightbox 2 for Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bs5-lightbox@1.8.3/dist/index.bundle.min.js"></script>

    <!-- Custom JS and CSS -->
    @vite(['resources/js/app.js'])
    @vite(['resources/css/custom.css'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Google Tag (gtag.js) -->
    <script async src=https://www.googletagmanager.com/gtag/js?id={{ config('services.gtag.tag') }}"></script>
    <script>
        var gtagTag = "{{ config('services.gtag.tag') }}";
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', gtagTag);
    </script>
</head>


<!-- Setting height to full viewport for themes to work properly -->

@php
    $view = isset($view) ? $view : 'none';
@endphp

<body data-view="{{ $view }}" style="min-height: 100vh;">
