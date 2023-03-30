<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, shrink-to-fit=no">
    <title>{{ config('app.name', 'ShippingXPS') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('theme/img/favicon.png') }}">

    <link id="googleFonts"
        href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800%7CShadows+Into+Light&display=swap"
        rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="{{ asset('theme/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/vendor/animate/animate.compat.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/vendor/simple-line-icons/css/simple-line-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/vendor/magnific-popup/magnific-popup.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/theme-elements.css') }}">
    <link id="skinCSS" rel="stylesheet" href="{{ asset('theme/css/skins/skin-business-consulting-3.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/custom.css') }}">

    @env('production')
    <script src="https://www.google.com/recaptcha/api.js?render=6LcKxb0hAAAAALPcMiT1eLu03DnQfxaluzJhgD8F"></script>
    <script
        src="https://www.paypal.com/sdk/js?client-id=Ad_mOnLAjPkl17HazcpuehUPrOIEP9rsM90Ta1BRuUSdvAe14-lcWx1ZWjCcESkSrqjJ_xjnogdy4ft6&enable-funding=venmo&currency=USD"
        data-sdk-integration-source="button-factory"></script>
    @endenv

    @env('staging')
    <script
        src="https://www.paypal.com/sdk/js?client-id=AZKXMPfJscqaryDzTCEnfpzP7CUT6rXYvS6EdQiX2FkCcSodMhqjYBmgBZvJLbRLonXetJ4BQClbYsJM&enable-funding=venmo&currency=USD"
        data-sdk-integration-source="button-factory"></script>
    @endenv

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-240686142-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-240686142-1');
    </script>

    @routes
    <link rel="stylesheet" href="{{ url(mix('css/app.css')) }}">
    <script src="{{ url(mix('js/app.js')) }}" defer></script>
</head>

<body class="font-sans antialiased">
    <div class="body">
        @inertia
    </div>
</body>

</html>