@php
    $settings = \App\Models\FrontendSetting::getSettings();
    use Illuminate\Support\Facades\Storage;
@endphp

<footer class="footer-wrapper">
    <div class="footer-container">
        <div class="footer-main-content">
            <!-- Haxo Shipping Column -->
            <div class="footer-column">
                <div class="footer-brand">
                    @if($settings->footer_logo && Storage::disk('public')->exists($settings->footer_logo))
                        <img src="{{ Storage::url($settings->footer_logo) }}" alt="Haxo Shipping" class="footer-logo">
                    @else
                        <div class="footer-brand-text">
                            <span class="footer-brand-name">Haxo</span>
                            <span class="footer-brand-subtitle">Shipping</span>
                        </div>
                    @endif
                </div>
                <p class="footer-description">{{ $settings->footer_description ?? 'Fastest platform with all courier service features. Help you start, run and grow your courier service.' }}</p>
                
                <!-- Download App Section -->
                <div class="footer-download-section">
                    <div class="footer-download-title">Download App</div>
                    <div class="footer-app-icons">
                        @if($settings->footer_google_play_url)
                            <a href="{{ $settings->footer_google_play_url }}" target="_blank" class="footer-app-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 20.5v-17c0-.59.34-1.11.84-1.35L13.69 12 3.84 20.85c-.5-.24-.84-.76-.84-1.35zm13.81-5.38L6.05 21.34l8.49-8.49 2.27 2.27zm-1.64-2.24l2.27 2.27L20.95 12l-3.78-3.78-2.27 2.27zM6.05 2.66l10.76 6.22-8.49 8.49-2.27-2.27L6.05 2.66z"/>
                                </svg>
                            </a>
                        @endif
                        @if($settings->footer_app_store_url)
                            <a href="{{ $settings->footer_app_store_url }}" target="_blank" class="footer-app-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.05 20.28c-.98.95-2.05.88-3.08.4-1.09-.5-2.08-.48-3.24 0-1.44.62-2.2.44-3.06-.4C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Available Services Column -->
            <div class="footer-column">
                <div class="footer-title">Available Services</div>
                <ul class="footer-links">
                    <li><a href="#">E-Commerce delivery</a></li>
                    <li><a href="#">Pick & Drop</a></li>
                    <li><a href="#">Packageing</a></li>
                    <li><a href="#">Warehousing</a></li>
                </ul>
            </div>

            <!-- About Column -->
            <div class="footer-column">
                <div class="footer-title">About</div>
                <ul class="footer-links">
                    <li><a href="{{ route('faq') }}">FAQ</a></li>
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="{{ route('contact') }}">Contact us</a></li>
                    <li><a href="{{ route('privacy-policy') }}">Privacy And Policy</a></li>
                    <li><a href="{{ route('terms-of-use') }}">Terms of Use</a></li>
                </ul>
            </div>

            <!-- Subscribe Us Column -->
            <div class="footer-column">
                <div class="footer-title">Subscribe Us</div>
                <p class="footer-subscribe-text">Get business news, tip and solutions to your problems our experts.</p>
                <form class="footer-subscribe-form" method="POST" action="#">
                    @csrf
                    <input type="email" name="email" class="footer-email-input" placeholder="Enter Email" required>
                    <button type="submit" class="footer-subscribe-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                    </button>
                </form>
                
                <!-- Social Section -->
                <div class="footer-social-section">
                    <div class="footer-social-title">Social</div>
                    <div class="footer-social-icons">
                        <!-- Facebook Icon -->
                        <a href="{{ $settings->footer_facebook_url ?? '#' }}" target="_blank" class="footer-social-icon" {{ !$settings->footer_facebook_url ? 'onclick="return false;"' : '' }}>
                            <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        
                        <!-- Instagram Icon -->
                        <a href="{{ $settings->footer_instagram_url ?? '#' }}" target="_blank" class="footer-social-icon" {{ !$settings->footer_instagram_url ? 'onclick="return false;"' : '' }}>
                            <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.98-6.98.058-1.28.072-1.689.072-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.98-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                        
                        <!-- Twitter/X Icon -->
                        <a href="{{ $settings->footer_twitter_url ?? '#' }}" target="_blank" class="footer-social-icon" {{ !$settings->footer_twitter_url ? 'onclick="return false;"' : '' }}>
                            <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                        
                        <!-- Skype Icon -->
                        <a href="{{ $settings->footer_skype_url ?? '#' }}" target="_blank" class="footer-social-icon" {{ !$settings->footer_skype_url ? 'onclick="return false;"' : '' }}>
                            <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" fill="currentColor"/>
                                <circle cx="12" cy="12" r="4" fill="white"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom Section -->
        <div class="footer-bottom">
            <div class="footer-separator"></div>
            
            <!-- Language Selector -->
            <div class="footer-languages">
                <a href="#" class="footer-language-link">English</a>
                <a href="#" class="footer-language-link">বাংলা</a>
                <a href="#" class="footer-language-link">हिन्दी</a>
                <a href="#" class="footer-language-link">عربي</a>
                <a href="#" class="footer-language-link">Franch</a>
                <a href="#" class="footer-language-link">Spanish</a>
                <a href="#" class="footer-language-link">Chinese</a>
            </div>
            
            <!-- Copyright -->
            <div class="footer-copyright">
                {{ $settings->footer_copyright_text ?? 'Copyright © All rights reserved. Development by Hexoship' }}
            </div>
        </div>
    </div>
</footer>


