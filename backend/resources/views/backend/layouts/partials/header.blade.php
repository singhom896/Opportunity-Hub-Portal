<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BRICS Artist Registry | @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/backend/v1/images/favicon.png') }}" />
	 <link rel="stylesheet" href="{{ asset('assets/backend/v1/css/bootstrap.min.css') }}" />
	

    <link rel="stylesheet" href="{{ asset('assets/backend/v1/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/backend/v1/css/responsive.css') }}" />
   
    @stack('styles')
</head>
<body>