@extends('layouts.app')
@section('title', 'Tracking - Haxo Shipping')
@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-3xl font-semibold mb-6">Tracking</h1>
    <p class="text-gray-600 mb-6">Enter your tracking number below to get live updates.</p>
    <form class="max-w-xl flex gap-3">
        <input class="border px-4 py-2 flex-1" placeholder="Enter tracking number" />
        <button class="px-4 py-2 bg-black text-white">Track Now</button>
    </form>
</div>
@endsection


