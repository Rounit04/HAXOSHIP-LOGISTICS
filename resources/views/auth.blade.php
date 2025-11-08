<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }} - Auth</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-white text-black">
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-semibold mb-4">Auth</h1>
            <p class="text-gray-600">Replace this with your authentication UI.</p>
            <a class="underline" href="{{ route('home') }}">Back to Home</a>
        </div>
    </body>
</html>


