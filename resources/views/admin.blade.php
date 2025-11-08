<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }} - Admin</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            :root {
                --admin-orange: #FF750F;
                --admin-orange-dark: #e6690d;
                --admin-gradient: linear-gradient(135deg, #FF750F 0%, #ff8c3a 100%);
                --admin-orange-gradient: linear-gradient(135deg, #FF750F 0%, #ff8c3a 100%);
            }
            
            * {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            }
            
            body {
                background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
                min-height: 100vh;
            }
            
            /* Mobile responsive adjustments */
            @media (max-width: 640px) {
                .admin-container {
                    padding-left: 1rem;
                    padding-right: 1rem;
                }
            }
        </style>
    </head>
    <body class="min-h-screen bg-white text-black">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 admin-container">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3 sm:mb-4">Admin</h1>
            <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">Admin dashboard placeholder.</p>
            <a class="text-sm sm:text-base underline hover:text-orange-600 transition-colors" href="{{ route('home') }}">Back to Home</a>
        </div>
    </body>
</html>


