<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @php
            $frontendSettings = \App\Models\FrontendSetting::getSettings();
            $siteName = $frontendSettings->site_name ?? config('app.name');
        @endphp
        <title>@yield('title', $siteName)</title>
        @if($frontendSettings->favicon && Storage::disk('public')->exists($frontendSettings->favicon))
            <link rel="icon" type="image/x-icon" href="{{ Storage::url($frontendSettings->favicon) }}">
            <link rel="shortcut icon" type="image/x-icon" href="{{ Storage::url($frontendSettings->favicon) }}">
        @endif
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @php
            $manifestPath = public_path('build/manifest.json');
            if (file_exists($manifestPath)) {
                $manifest = json_decode(file_get_contents($manifestPath), true);
                $cssFile = $manifest['resources/css/app.css']['file'] ?? 'assets/app-D8se_Iem.css';
                $jsFile = $manifest['resources/js/app.js']['file'] ?? 'assets/app-CvgioS1y.js';
            } else {
                $cssFile = 'assets/app-D8se_Iem.css';
                $jsFile = 'assets/app-CvgioS1y.js';
            }
        @endphp
        <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
        <script type="module" src="{{ asset('build/' . $jsFile) }}"></script>
    </head>
    <body class="min-h-screen flex flex-col bg-white text-black">
        @include('partials.header')
        <main class="flex-1">@yield('content')</main>
        @include('partials.footer')
        @include('partials.gdpr-cookie-banner')
    </body>
    </html>


