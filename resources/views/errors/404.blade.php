<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Page Not Found</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-white text-black">
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-semibold mb-2">404 - Not Found</h1>
            <p class="text-gray-600 mb-4">The page you are looking for could not be found.</p>
            <a class="underline" href="{{ route('home') }}">Go Home</a>
        </div>
    </body>
</html>


