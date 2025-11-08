@php
    $settings = \App\Models\FrontendSetting::getSettings();
@endphp

@if($settings->gdpr_cookie_enabled)
    <div id="gdpr-cookie-banner" class="gdpr-cookie-banner" style="display: none; position: fixed; {{ $settings->gdpr_cookie_position === 'top' ? 'top: 0;' : 'bottom: 0;' }} left: 0; right: 0; z-index: 9999; background-color: {{ $settings->gdpr_cookie_bg_color ?? '#ffffff' }}; color: {{ $settings->gdpr_cookie_text_color ?? '#1b1b18' }}; padding: 20px; box-shadow: 0 -2px 10px rgba(0,0,0,0.1);">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex-1">
                    <p class="text-sm md:text-base" style="color: {{ $settings->gdpr_cookie_text_color ?? '#1b1b18' }};">
                        {{ $settings->gdpr_cookie_message ?? 'We use cookies to enhance your browsing experience, serve personalized ads or content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.' }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button id="gdpr-cookie-accept" class="px-5 py-2.5 rounded-lg font-semibold text-sm text-white transition-all hover:opacity-90" style="background-color: {{ $settings->gdpr_cookie_button_color ?? '#FF750F' }};">
                        {{ $settings->gdpr_cookie_button_text ?? 'Accept All' }}
                    </button>
                    <button id="gdpr-cookie-decline" class="px-5 py-2.5 rounded-lg font-semibold text-sm border-2 transition-all hover:bg-gray-50" style="border-color: {{ $settings->gdpr_cookie_text_color ?? '#1b1b18' }}; color: {{ $settings->gdpr_cookie_text_color ?? '#1b1b18' }};">
                        {{ $settings->gdpr_cookie_decline_text ?? 'Decline' }}
                    </button>
                    <button id="gdpr-cookie-settings" class="px-5 py-2.5 rounded-lg font-semibold text-sm border-2 transition-all hover:bg-gray-50" style="border-color: {{ $settings->gdpr_cookie_text_color ?? '#1b1b18' }}; color: {{ $settings->gdpr_cookie_text_color ?? '#1b1b18' }};">
                        {{ $settings->gdpr_cookie_settings_text ?? 'Settings' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .gdpr-cookie-banner {
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .gdpr-cookie-banner[data-position="top"] {
            animation: slideDown 0.5s ease-out;
        }
        
        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .gdpr-cookie-banner.hidden {
            animation: slideDownOut 0.5s ease-out forwards;
        }
        
        @keyframes slideDownOut {
            to {
                transform: translateY(100%);
                opacity: 0;
            }
        }
        
        .gdpr-cookie-banner[data-position="top"].hidden {
            animation: slideUpOut 0.5s ease-out forwards;
        }
        
        @keyframes slideUpOut {
            to {
                transform: translateY(-100%);
                opacity: 0;
            }
        }
    </style>

    <script>
        (function() {
            const banner = document.getElementById('gdpr-cookie-banner');
            const acceptBtn = document.getElementById('gdpr-cookie-accept');
            const declineBtn = document.getElementById('gdpr-cookie-decline');
            const settingsBtn = document.getElementById('gdpr-cookie-settings');
            const cookieName = 'gdpr_cookie_consent';
            const expiryDays = {{ $settings->gdpr_cookie_expiry_days ?? 365 }};
            
            // Check if consent already given
            function getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
                return null;
            }
            
            function setCookie(name, value, days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                const expires = "expires=" + date.toUTCString();
                document.cookie = `${name}=${value};${expires};path=/;SameSite=Lax`;
            }
            
            function hideBanner() {
                banner.classList.add('hidden');
                setTimeout(() => {
                    banner.style.display = 'none';
                }, 500);
            }
            
            // Show banner if consent not given
            if (!getCookie(cookieName)) {
                banner.style.display = 'block';
                banner.setAttribute('data-position', '{{ $settings->gdpr_cookie_position ?? "bottom" }}');
            }
            
            // Accept button
            acceptBtn.addEventListener('click', function() {
                setCookie(cookieName, 'accepted', expiryDays);
                hideBanner();
            });
            
            // Decline button
            declineBtn.addEventListener('click', function() {
                setCookie(cookieName, 'declined', expiryDays);
                hideBanner();
            });
            
            // Settings button (for now, just hide - can be extended later)
            settingsBtn.addEventListener('click', function() {
                // You can extend this to show a modal with detailed cookie settings
                setCookie(cookieName, 'settings', expiryDays);
                hideBanner();
            });
        })();
    </script>
@endif






