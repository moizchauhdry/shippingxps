<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Mobile Metas -->
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, shrink-to-fit=no">


		<!-- Web Fonts  -->
		<link id="googleFonts" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800%7CShadows+Into+Light&display=swap" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->
		<link rel="stylesheet" href="{{ asset('theme/vendor/bootstrap/css/bootstrap.min.css') }}">
		<link rel="stylesheet" href="{{ asset('theme/vendor/fontawesome-free/css/all.min.css') }}">
		<link rel="stylesheet" href="{{ asset('theme/vendor/animate/animate.compat.css') }}">
		<link rel="stylesheet" href="{{ asset('theme/vendor/simple-line-icons/css/simple-line-icons.min.css') }}">
		<link rel="stylesheet" href="{{ asset('theme/vendor/magnific-popup/magnific-popup.min.css') }}">

		<!-- Theme CSS -->
		<link rel="stylesheet" href="{{ asset('theme/css/theme.css') }}">
		<link rel="stylesheet" href="{{ asset('theme/css/theme-elements.css') }}">

        
		<!-- Demo CSS -->
		<link rel="stylesheet" href="{{ asset('theme/css/demos/demo-business-consulting-3.css') }}">

		<!-- Skin CSS -->
		<link id="skinCSS" rel="stylesheet" href="{{ asset('theme/css/skins/skin-business-consulting-3.css') }}">

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="{{ asset('theme/css/custom.css') }}">

		<!-- Head Libs -->
		<script src="{{ asset('theme/vendor/modernizr/modernizr.min.js') }}"></script>

        <!-- Styles -->
        <link rel="stylesheet" href="{{ url(mix('css/app.css')) }}">

        <!-- Scripts -->
        @routes
        <script src="{{ url(mix('js/app.js')) }}" defer></script>
    </head>
    <body class="font-sans antialiased">
        <div class="body">
        
              @inertia
        </div>
      

        @env ('local')
            <script src="http://localhost:8000/browser-sync/browser-sync-client.js"></script>
        @endenv
       <!-- Vendor -->

    </body>
</html>