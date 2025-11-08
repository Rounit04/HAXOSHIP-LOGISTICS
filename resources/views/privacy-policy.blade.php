@extends('layouts.app')
@section('title', 'Privacy And Policy - Haxo Shipping')
@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-3xl font-semibold mb-6">Privacy And Policy</h1>
    
    <div class="max-w-3xl space-y-6 text-gray-700">
        <section>
            <h2 class="text-xl font-semibold mb-3 text-black">1. Introduction</h2>
            <p class="mb-4">At Haxo Shipping, we are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our courier services and website.</p>
        </section>
        
        <section>
            <h2 class="text-xl font-semibold mb-3 text-black">2. Information We Collect</h2>
            <p class="mb-2">We collect information that you provide directly to us, including:</p>
            <ul class="list-disc pl-6 mb-4 space-y-1">
                <li>Personal identification information (name, email address, phone number, address)</li>
                <li>Payment information and billing details</li>
                <li>Shipping and delivery addresses</li>
                <li>Account credentials and preferences</li>
                <li>Communication records when you contact our support team</li>
            </ul>
            <p class="mb-4">We also automatically collect certain information when you visit our website, such as your IP address, browser type, device information, and usage patterns.</p>
        </section>
        
        <section>
            <h2 class="text-xl font-semibold mb-3 text-black">3. How We Use Your Information</h2>
            <p class="mb-2">We use the information we collect to:</p>
            <ul class="list-disc pl-6 mb-4 space-y-1">
                <li>Process and fulfill your shipping requests</li>
                <li>Provide customer support and respond to inquiries</li>
                <li>Send tracking updates and delivery notifications</li>
                <li>Process payments and prevent fraud</li>
                <li>Improve our services and website functionality</li>
                <li>Send marketing communications (with your consent)</li>
                <li>Comply with legal obligations</li>
            </ul>
        </section>
        
        <section>
            <h2 class="text-xl font-semibold mb-3 text-black">4. Information Sharing and Disclosure</h2>
            <p class="mb-4">We do not sell your personal information. We may share your information with:</p>
            <ul class="list-disc pl-6 mb-4 space-y-1">
                <li>Service providers who assist in our operations (payment processors, delivery partners)</li>
                <li>Law enforcement agencies when required by law</li>
                <li>Business partners in case of mergers or acquisitions</li>
                <li>With your explicit consent</li>
            </ul>
        </section>
        
        <section>
            <h2 class="text-xl font-semibold mb-3 text-black">5. Data Security</h2>
            <p class="mb-4">We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the internet is 100% secure, and we cannot guarantee absolute security.</p>
        </section>
        
        <section>
            <h2 class="text-xl font-semibold mb-3 text-black">6. Your Rights</h2>
            <p class="mb-2">You have the right to:</p>
            <ul class="list-disc pl-6 mb-4 space-y-1">
                <li>Access and receive a copy of your personal data</li>
                <li>Correct inaccurate or incomplete information</li>
                <li>Request deletion of your personal data</li>
                <li>Object to processing of your personal data</li>
                <li>Withdraw consent at any time</li>
                <li>Data portability</li>
            </ul>
        </section>
        
        <section>
            <h2 class="text-xl font-semibold mb-3 text-black">7. Cookies and Tracking Technologies</h2>
            <p class="mb-4">We use cookies and similar tracking technologies to enhance your browsing experience, analyze website traffic, and personalize content. You can control cookie preferences through your browser settings.</p>
        </section>
        
        <section>
            <h2 class="text-xl font-semibold mb-3 text-black">8. Third-Party Links</h2>
            <p class="mb-4">Our website may contain links to third-party websites. We are not responsible for the privacy practices of these external sites. We encourage you to review their privacy policies before providing any personal information.</p>
        </section>
        
        <section>
            <h2 class="text-xl font-semibold mb-3 text-black">9. Children's Privacy</h2>
            <p class="mb-4">Our services are not intended for individuals under the age of 18. We do not knowingly collect personal information from children. If you believe we have collected information from a child, please contact us immediately.</p>
        </section>
        
        <section>
            <h2 class="text-xl font-semibold mb-3 text-black">10. Changes to This Privacy Policy</h2>
            <p class="mb-4">We may update this Privacy Policy from time to time. We will notify you of any significant changes by posting the new policy on this page and updating the "Last Updated" date. Your continued use of our services after changes become effective constitutes acceptance of the updated policy.</p>
        </section>
        
        <section>
            <h2 class="text-xl font-semibold mb-3 text-black">11. Contact Us</h2>
            <p class="mb-4">If you have any questions or concerns about this Privacy Policy or our data practices, please contact us at:</p>
            <p class="mb-2">
                <strong>Email:</strong> <a href="mailto:info@haxoship.com" class="underline text-blue-600">info@haxoship.com</a><br>
                <strong>Phone:</strong> 8130465575
            </p>
        </section>
        
        <section class="pt-4">
            <p class="text-sm text-gray-500">Last Updated: {{ date('F d, Y') }}</p>
        </section>
    </div>
</div>
@endsection

