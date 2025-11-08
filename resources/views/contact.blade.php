@extends('layouts.app')
@section('title', 'Contact - Haxo Shipping')
@section('content')
<div class="container mx-auto px-4 py-12 max-w-6xl">
    <h1 class="text-3xl font-semibold mb-6">Contact Us</h1>
    <p class="text-gray-600 mb-8">Take a look at the most commonly asked questions.</p>
    
    <div class="grid md:grid-cols-2 gap-8 lg:gap-12">
        <!-- Contact Form Section (Left) -->
        <div>
            <form class="space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Enter name" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Enter email" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Subject <span class="text-red-500">*</span></label>
                    <input type="text" name="subject" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Enter subject" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Message <span class="text-red-500">*</span></label>
                    <textarea name="message" required rows="6" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 resize-y focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Enter your message"></textarea>
                </div>
                <div>
                    <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white font-semibold rounded-lg hover:bg-orange-600 transition-colors shadow-md hover:shadow-lg">
                        Submit
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Contact Information Section (Right) -->
        <div>
            <h2 class="text-3xl font-semibold mb-6">Address:</h2>
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-gray-700 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <div>
                        <span class="text-gray-900 font-medium">Email : </span>
                        <a href="mailto:{{ $settings->contact_email ?? 'haxoshipping@gmail.com' }}" class="text-gray-700 hover:text-orange-500 transition-colors">
                            {{ $settings->contact_email ?? 'haxoshipping@gmail.com' }}
                        </a>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-gray-700 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <div>
                        <span class="text-gray-900 font-medium">Phone : </span>
                        <a href="tel:{{ $settings->contact_phone ?? '8130465575' }}" class="text-gray-700 hover:text-orange-500 transition-colors">
                            {{ $settings->contact_phone ?? '8130465575' }}
                        </a>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-gray-700 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <div>
                        <span class="text-gray-900 font-medium">Address: </span>
                        <span class="text-gray-700">{{ $settings->contact_address ?? 'a-123, dellhi' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


