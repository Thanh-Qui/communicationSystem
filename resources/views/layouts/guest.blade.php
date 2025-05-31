<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Chat Online</title>
        <link rel="shortcut icon" type="image/x-icon" href=" {{ asset('assets/img/logoChat.png') }} ">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div style="background-color: #f1f7f7" class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div>
                
            </div>
            <div>
                @if (Request::is('forgot-password'))
                    <h1 style="font-size: 48px"><strong><span style="color: red">Forgot </span>Password</strong> </h1>
                @elseif (Request::is('register'))
                    <h1 style="font-size: 48px"><strong><span style="color: red">R </span>e g i s t e r</strong> </h1>
                    <p style="font-size: 18px; text-align: center; color: #0000009e">Create a new account</p>
                @elseif (Request::is('reset-password/*')) 
                    <h1 style="font-size: 48px"><strong><span style="color: red">Reset </span>Password</strong></h1>
                @endif
                
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
