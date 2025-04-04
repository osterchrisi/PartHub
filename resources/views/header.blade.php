@php
    $view = isset($view) ? $view : 'none';
    $title = isset($title) ? $title : '';
@endphp
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords"
        content="inventory management, electronics part inventory, inventory, electronics inventory, BOMs (bills of materials), BOM, production tracking, stock keeping, supply chain management, warehouse management, parts tracking, component tracking, parts inventory, component inventory, self-hosted inventory management, cloud-based inventory management, inventory software, open-source, electronic part inventory, BOM creation, BOM execution">
    <meta name="description"
        content="Your simple, friendly electronic parts inventory with BOM management. Simple. Free tiers and self-hosting. Aimed at small electronic makers and tinkerers">
    <title>PartHub - {{ $title }}
    </title>

    {{-- Cookie Consent, GDPR --}}
    <!-- Termly custom blocking map -->
    <script data-termly-config>
        window.TERMLY_CUSTOM_BLOCKING_MAP = {
            "127.0.0.1": "essential",
            "unpkg.com": "essential",
            "parthub.online": "essential",
        }
    </script>
    <script type="text/javascript"
        src="https://app.termly.io/resource-blocker/4134b801-03f7-4cab-85ea-c4ab4e329670?autoBlock=on"></script>
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

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <script src="https://cdn.jsdelivr.net/gh/akottr/dragtable@master/jquery.dragtable.js"></script>
    <script
        src="https://unpkg.com/bootstrap-table@1.21.3/dist/extensions/reorder-columns/bootstrap-table-reorder-columns.js">
    </script>
    <!-- Bootstrap Table Cookie to remember table state -->
    <script src="https://unpkg.com/bootstrap-table@1.21.3/dist/extensions/cookie/bootstrap-table-cookie.min.js"></script>

    <!-- Treegrid and Bootstrap-Table Treegrid Plugin -->
    <link href="https://cdn.jsdelivr.net/npm/jquery-treegrid@0.3.0/css/jquery.treegrid.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery-treegrid@0.3.0/js/jquery.treegrid.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.4/dist/extensions/treegrid/bootstrap-table-treegrid.min.js">
    </script>

    <!-- Bootstrap Show Password -->
    {{-- <script src="https://unpkg.com/bootstrap-show-password@1.3.0/dist/bootstrap-show-password.min.js"></script> --}}

    <!-- Lightbox 2 for Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bs5-lightbox@1.8.3/dist/index.bundle.min.js"></script>

    <!-- Sortable JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>


    <!-- Custom JS and CSS -->
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Tag Manager -->
    <script>
        const gtagTagManager = "{{ config('services.gtag.tag-manager') }}";
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', gtagTagManager);
    </script>
    <!-- End Google Tag Manager -->
</head>

<body data-view="{{ $view }}">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5DKCXTZW" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
