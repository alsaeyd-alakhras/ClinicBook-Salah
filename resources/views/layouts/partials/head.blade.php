@props([
    'title' => 'title',
    'template' => 'template',
])
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="rtl" data-theme="theme-default" data-assets-path="{{ asset('assets') }}/" data-template="{{ $template }}" data-style="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>{{ $title }}</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('imgs/icon.png')}}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css')}}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css')}}" />
    {{-- <!-- <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css')}}" /> --> --}}

    <!-- Core CSS -->

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css')}}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css')}}" class="template-customizer-theme-css" />

    <link rel="stylesheet" href="{{ asset('assets/css/demo.css')}}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css')}}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js')}}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    {{-- <script src="{{ asset('assets/vendor/js/template-customizer.js')}}"></script> --}}

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js')}}"></script>

    <style>
        .avatar img{
            object-fit: cover;
        }
        body{
            font-family: "Almarai", serif;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-semi-dark.css')}}" />
    @stack('styles')
</head>

<body>
