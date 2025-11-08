@extends('layouts.app')
@section('title', 'About - Haxo Shipping')
@section('content')
@php
    $settings = \App\Models\FrontendSetting::getSettings();
@endphp
<div class="container mx-auto px-4 py-12">
    <h1 class="text-3xl font-semibold mb-6">About</h1>
    <div class="max-w-3xl text-gray-700">
        @if($settings->about_us_content)
            <div class="prose prose-lg max-w-none">
                {!! nl2br(e($settings->about_us_content)) !!}
            </div>
        @else
            <p class="text-gray-600">Haxo Shipping delivers hassle-free, fast, and reliable courier services with features like COD, limitless pickup, secure handling, and live tracking updates.</p>
        @endif
    </div>
</div>
@endsection


