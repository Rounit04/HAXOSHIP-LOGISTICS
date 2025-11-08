@extends('layouts.app')
@section('title', 'FAQ - Haxo Shipping')
@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-3xl font-semibold mb-6">Frequently Asked Questions</h1>
    
    <div class="max-w-3xl space-y-6">
        <div class="border-b pb-4">
            <h2 class="text-xl font-semibold mb-2">What is Haxo Shipping?</h2>
            <p class="text-gray-600">Haxo Shipping is a comprehensive courier service platform that provides fast, reliable delivery services with features like COD, limitless pickup, secure handling, and live tracking updates.</p>
        </div>
        
        <div class="border-b pb-4">
            <h2 class="text-xl font-semibold mb-2">How can I track my shipment?</h2>
            <p class="text-gray-600">You can track your shipment by visiting our tracking page and entering your tracking number or AWB (Air Waybill) number. You'll receive real-time updates on your package's location and status.</p>
        </div>
        
        <div class="border-b pb-4">
            <h2 class="text-xl font-semibold mb-2">What payment methods do you accept?</h2>
            <p class="text-gray-600">We accept various payment methods including cash on delivery (COD), online payments, and bank transfers. Payment options may vary depending on your location and service type.</p>
        </div>
        
        <div class="border-b pb-4">
            <h2 class="text-xl font-semibold mb-2">What are your delivery times?</h2>
            <p class="text-gray-600">Delivery times vary depending on the destination and service type. Standard deliveries typically take 2-5 business days, while express services are available for faster delivery options. You can check specific delivery times during the booking process.</p>
        </div>
        
        <div class="border-b pb-4">
            <h2 class="text-xl font-semibold mb-2">How do I schedule a pickup?</h2>
            <p class="text-gray-600">You can schedule a pickup through your dashboard after creating an account. Simply log in, create a new booking, and select your preferred pickup date and time. Our team will collect your package at the scheduled time.</p>
        </div>
        
        <div class="border-b pb-4">
            <h2 class="text-xl font-semibold mb-2">What if my package is damaged or lost?</h2>
            <p class="text-gray-600">We take package security seriously. If your package is damaged or lost, please contact our customer support immediately. We have insurance and compensation policies in place to handle such situations. Please keep your tracking number and receipt for reference.</p>
        </div>
        
        <div class="border-b pb-4">
            <h2 class="text-xl font-semibold mb-2">Do you offer international shipping?</h2>
            <p class="text-gray-600">Yes, we offer international shipping services to various countries. International shipping rates and delivery times vary by destination. Please check our pricing page or contact us for specific international shipping information.</p>
        </div>
        
        <div class="pb-4">
            <h2 class="text-xl font-semibold mb-2">How can I contact customer support?</h2>
            <p class="text-gray-600">You can reach our customer support team through our contact page, email us at <a href="mailto:info@haxoship.com" class="underline text-blue-600">info@haxoship.com</a>, or call us at 8130465575. Our support team is available to assist you with any questions or concerns.</p>
        </div>
    </div>
</div>
@endsection

