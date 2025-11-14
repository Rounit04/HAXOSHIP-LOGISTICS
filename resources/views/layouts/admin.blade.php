<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Haxo Shipping') }}</title>
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
            }
            
            .admin-header {
                background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
                backdrop-filter: blur(10px);
                box-shadow: 0 2px 12px rgba(0,0,0,0.05);
                border-bottom: 1px solid rgba(0,0,0,0.06);
            }
            
            .admin-sidebar {
                background: linear-gradient(180deg, #ffffff 0%, #fafbfc 100%);
                box-shadow: 2px 0 16px rgba(0,0,0,0.04);
                border-right: 1px solid rgba(0,0,0,0.06);
            }
            
            .nav-link {
                position: relative;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                color: #4b5563;
            }
            
            .nav-link:hover {
                background: linear-gradient(90deg, rgba(255, 245, 237, 0.8) 0%, rgba(255, 232, 214, 0.6) 100%);
                transform: translateX(4px);
                color: #FF750F;
            }
            
            .nav-link:hover .w-10 {
                background: rgba(255, 117, 15, 0.15) !important;
            }
            
            .nav-link:hover .w-10 svg {
                color: #FF750F !important;
            }
            
            .nav-link.active {
                background: var(--admin-gradient);
                color: white;
                box-shadow: 0 4px 16px rgba(255, 117, 15, 0.35);
                transform: translateX(0);
            }
            
            .nav-link.active svg {
                color: white !important;
            }
            
            .nav-link.active::before {
                content: '';
                position: absolute;
                left: 0;
                top: 50%;
                transform: translateY(-50%);
                width: 4px;
                height: 70%;
                background: var(--admin-orange-gradient);
                border-radius: 0 4px 4px 0;
                box-shadow: 0 0 8px rgba(255, 117, 15, 0.5);
            }
            
            .nav-link.active span {
                color: white !important;
                font-weight: 700;
            }
            
            .admin-sidebar::-webkit-scrollbar {
                width: 6px;
            }
            
            .admin-sidebar::-webkit-scrollbar-track {
                background: transparent;
            }
            
            .admin-sidebar::-webkit-scrollbar-thumb {
                background: rgba(255, 117, 15, 0.3);
                border-radius: 3px;
            }
            
            .admin-sidebar::-webkit-scrollbar-thumb:hover {
                background: rgba(255, 117, 15, 0.5);
            }
            
            /* Mobile Responsive Styles */
            @media (max-width: 1023px) {
                .admin-sidebar {
                    position: fixed;
                    left: -100%;
                    top: 0;
                    bottom: 0;
                    z-index: 50;
                    transition: left 0.3s ease-in-out;
                    height: 100vh;
                    width: 280px;
                    max-width: 85vw;
                }
                
                .admin-sidebar.mobile-open {
                    left: 0;
                }
                
                .sidebar-overlay {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.5);
                    z-index: 40;
                    backdrop-filter: blur(2px);
                }
                
                .sidebar-overlay.active {
                    display: block;
                }
                
                .admin-header {
                    padding-left: 1rem;
                    padding-right: 1rem;
                }
                
                .notification-dropdown-mobile {
                    right: 0.5rem !important;
                    left: 0.5rem !important;
                    width: auto !important;
                    max-width: calc(100vw - 1rem) !important;
                }
            }
            
            /* Desktop - Ensure sidebar is always visible */
            @media (min-width: 1024px) {
                .admin-sidebar {
                    position: relative !important;
                    left: 0 !important;
                }
                
                .sidebar-overlay {
                    display: none !important;
                }
            }
            
            .submenu-arrow {
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .submenu-arrow.open,
            .submenu-arrow.rotate-90 {
                transform: rotate(90deg);
            }
            .submenu-link {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                border-radius: 10px;
                margin-bottom: 4px;
            }
            .submenu-link:hover {
                background: linear-gradient(90deg, rgba(255, 117, 15, 0.08) 0%, rgba(255, 117, 15, 0.04) 100%);
                transform: translateX(4px);
                box-shadow: 0 2px 6px rgba(255, 117, 15, 0.08);
            }
            .submenu-link.active {
                background: linear-gradient(135deg, rgba(255, 117, 15, 0.95) 0%, rgba(255, 140, 58, 0.95) 100%);
                color: white !important;
                border-left: 3px solid #f97316;
                box-shadow: 0 4px 12px rgba(255, 117, 15, 0.25);
                transform: translateX(2px);
            }
            .submenu-link.active span {
                color: white !important;
                font-weight: 600;
            }
            .submenu-link.active svg {
                color: white !important;
            }
            .submenu-link.active .w-8 {
                background: rgba(255, 255, 255, 0.2) !important;
                backdrop-filter: blur(10px);
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }
            .border-l-3 {
                border-left-width: 3px;
            }
        </style>
        <script>
            function toggleSubmenu(submenuId) {
                const submenu = document.getElementById(submenuId);
                const arrow = document.getElementById(submenuId + '-arrow');
                if (submenu) {
                    const isHidden = submenu.style.display === 'none' || 
                                   submenu.classList.contains('hidden') || 
                                   submenu.style.maxHeight === '0px' ||
                                   submenu.style.opacity === '0';
                    if (isHidden) {
                        // Show submenu
                        submenu.classList.remove('hidden');
                        submenu.style.display = 'block';
                        // Use larger max-height for reports submenu to show all items including Bank Report
                        const maxHeight = submenuId === 'reports-submenu' ? '800px' : '200px';
                        const maxHeightClass = submenuId === 'reports-submenu' ? 'max-h-[800px]' : 'max-h-40';
                        submenu.style.maxHeight = maxHeight;
                        submenu.style.opacity = '1';
                        setTimeout(() => {
                            submenu.classList.remove('max-h-0', 'opacity-0');
                            submenu.classList.add(maxHeightClass, 'opacity-100');
                        }, 10);
                    } else {
                        // Hide submenu
                        const maxHeightClass = submenuId === 'reports-submenu' ? 'max-h-[800px]' : 'max-h-40';
                        submenu.classList.remove(maxHeightClass, 'opacity-100');
                        submenu.classList.add('max-h-0', 'opacity-0');
                        submenu.style.maxHeight = '0';
                        submenu.style.opacity = '0';
                        setTimeout(() => {
                            submenu.classList.add('hidden');
                            submenu.style.display = 'none';
                        }, 300);
                    }
                    if (arrow) {
                        arrow.classList.toggle('rotate-90');
                    }
                }
            }
            
            // Auto-expand Networks submenu if on network pages
            document.addEventListener('DOMContentLoaded', function() {
                @if(request()->routeIs('admin.networks*'))
                    const networksSubmenu = document.getElementById('networks-submenu');
                    const networksArrow = document.getElementById('networks-submenu-arrow');
                    if (networksSubmenu) {
                        networksSubmenu.classList.remove('hidden');
                        networksSubmenu.style.display = 'block';
                        networksSubmenu.style.maxHeight = '200px';
                        networksSubmenu.style.opacity = '1';
                        networksSubmenu.classList.remove('max-h-0', 'opacity-0');
                        networksSubmenu.classList.add('max-h-40', 'opacity-100');
                        if (networksArrow) {
                            networksArrow.classList.add('rotate-90');
                        }
                    }
                @endif
                
                // Auto-expand Rate Calculator submenu if on rate calculator pages
                @if(request()->routeIs('admin.rate-calculator*'))
                    // No submenu for rate calculator
                @endif
                
                // Auto-expand Search with AWB submenu if on AWB pages
                @if(request()->routeIs('admin.search-with-awb*'))
                    const searchAwbSubmenu = document.getElementById('search-awb-submenu');
                    const searchAwbArrow = document.getElementById('search-awb-submenu-arrow');
                    if (searchAwbSubmenu) {
                        searchAwbSubmenu.classList.remove('hidden');
                        searchAwbSubmenu.style.display = 'block';
                        searchAwbSubmenu.style.maxHeight = '200px';
                        searchAwbSubmenu.style.opacity = '1';
                        searchAwbSubmenu.classList.remove('max-h-0', 'opacity-0');
                        searchAwbSubmenu.classList.add('max-h-40', 'opacity-100');
                        if (searchAwbArrow) {
                            searchAwbArrow.classList.add('rotate-90');
                        }
                    }
                @endif
                
                // Auto-expand Settings submenu if on settings pages
                @if(request()->routeIs('admin.settings*') || request()->routeIs('admin.delivery-category*') || request()->routeIs('admin.delivery-charge*') || request()->routeIs('admin.delivery-type*') || request()->routeIs('admin.liquid-fragile*') || request()->routeIs('admin.sms-settings*') || request()->routeIs('admin.sms-send-settings*') || request()->routeIs('admin.googlemap-settings*') || request()->routeIs('admin.mail-settings*') || request()->routeIs('admin.social-login*') || request()->routeIs('admin.payment-setup*') || request()->routeIs('admin.packaging*') || request()->routeIs('admin.currency*'))
                    const settingsSubmenu = document.getElementById('settings-submenu');
                    const settingsArrow = document.getElementById('settings-submenu-arrow');
                    if (settingsSubmenu) {
                        settingsSubmenu.classList.remove('hidden');
                        settingsSubmenu.style.display = 'block';
                        settingsSubmenu.style.maxHeight = '200px';
                        settingsSubmenu.style.opacity = '1';
                        settingsSubmenu.classList.remove('max-h-0', 'opacity-0');
                        settingsSubmenu.classList.add('max-h-[600px]', 'opacity-100');
                        settingsSubmenu.style.maxHeight = '600px';
                        if (settingsArrow) {
                            settingsArrow.classList.add('rotate-90');
                        }
                    }
                @endif
                
                // Auto-expand Services submenu if on service pages
                @if(request()->routeIs('admin.services*'))
                    const servicesSubmenu = document.getElementById('services-submenu');
                    const servicesArrow = document.getElementById('services-submenu-arrow');
                    if (servicesSubmenu) {
                        servicesSubmenu.classList.remove('hidden');
                        servicesSubmenu.style.display = 'block';
                        servicesSubmenu.style.maxHeight = '200px';
                        servicesSubmenu.style.opacity = '1';
                        servicesSubmenu.classList.remove('max-h-0', 'opacity-0');
                        servicesSubmenu.classList.add('max-h-40', 'opacity-100');
                        if (servicesArrow) {
                            servicesArrow.classList.add('rotate-90');
                        }
                    }
                @endif
                
                // Auto-expand Countries submenu if on country pages
                @if(request()->routeIs('admin.countries*'))
                    const countriesSubmenu = document.getElementById('countries-submenu');
                    const countriesArrow = document.getElementById('countries-submenu-arrow');
                    if (countriesSubmenu) {
                        countriesSubmenu.classList.remove('hidden');
                        countriesSubmenu.style.display = 'block';
                        countriesSubmenu.style.maxHeight = '200px';
                        countriesSubmenu.style.opacity = '1';
                        countriesSubmenu.classList.remove('max-h-0', 'opacity-0');
                        countriesSubmenu.classList.add('max-h-40', 'opacity-100');
                        if (countriesArrow) {
                            countriesArrow.classList.add('rotate-90');
                        }
                    }
                @endif
                
                // Auto-expand Zones submenu if on zone pages
                @if(request()->routeIs('admin.zones*'))
                    const zonesSubmenu = document.getElementById('zones-submenu');
                    const zonesArrow = document.getElementById('zones-submenu-arrow');
                    if (zonesSubmenu) {
                        zonesSubmenu.classList.remove('hidden');
                        zonesSubmenu.style.display = 'block';
                        zonesSubmenu.style.maxHeight = '200px';
                        zonesSubmenu.style.opacity = '1';
                        zonesSubmenu.classList.remove('max-h-0', 'opacity-0');
                        zonesSubmenu.classList.add('max-h-40', 'opacity-100');
                        if (zonesArrow) {
                            zonesArrow.classList.add('rotate-90');
                        }
                    }
                @endif
                
                // Auto-expand Shipping Charges submenu if on shipping charges pages
                @if(request()->routeIs('admin.shipping-charges*'))
                    const shippingChargesSubmenu = document.getElementById('shipping-charges-submenu');
                    const shippingChargesArrow = document.getElementById('shipping-charges-submenu-arrow');
                    if (shippingChargesSubmenu) {
                        shippingChargesSubmenu.classList.remove('hidden');
                        shippingChargesSubmenu.style.display = 'block';
                        shippingChargesSubmenu.style.maxHeight = '200px';
                        shippingChargesSubmenu.style.opacity = '1';
                        shippingChargesSubmenu.classList.remove('max-h-0', 'opacity-0');
                        shippingChargesSubmenu.classList.add('max-h-40', 'opacity-100');
                        if (shippingChargesArrow) {
                            shippingChargesArrow.classList.add('rotate-90');
                        }
                    }
                @endif
                
                // Auto-expand Formulas submenu if on formula pages
                @if(request()->routeIs('admin.formulas*'))
                    const formulasSubmenu = document.getElementById('formulas-submenu');
                    const formulasArrow = document.getElementById('formulas-submenu-arrow');
                    if (formulasSubmenu) {
                        formulasSubmenu.classList.remove('hidden');
                        formulasSubmenu.style.display = 'block';
                        formulasSubmenu.style.maxHeight = '200px';
                        formulasSubmenu.style.opacity = '1';
                        formulasSubmenu.classList.remove('max-h-0', 'opacity-0');
                        formulasSubmenu.classList.add('max-h-40', 'opacity-100');
                        if (formulasArrow) {
                            formulasArrow.classList.add('rotate-90');
                        }
                    }
                @endif
                
                // Auto-expand AWB Upload submenu if on AWB upload pages
                @if(request()->routeIs('admin.awb-upload*'))
                    const awbUploadSubmenu = document.getElementById('awb-upload-submenu');
                    const awbUploadArrow = document.getElementById('awb-upload-submenu-arrow');
                    if (awbUploadSubmenu) {
                        awbUploadSubmenu.classList.remove('hidden');
                        awbUploadSubmenu.style.display = 'block';
                        awbUploadSubmenu.style.maxHeight = '200px';
                        awbUploadSubmenu.style.opacity = '1';
                        awbUploadSubmenu.classList.remove('max-h-0', 'opacity-0');
                        awbUploadSubmenu.classList.add('max-h-40', 'opacity-100');
                        if (awbUploadArrow) {
                            awbUploadArrow.classList.add('rotate-90');
                        }
                    }
                @endif
                
                // Auto-expand Bookings submenu if on booking pages
                @if(request()->routeIs('admin.bookings*'))
                    const bookingsSubmenu = document.getElementById('bookings-submenu');
                    const bookingsArrow = document.getElementById('bookings-submenu-arrow');
                    if (bookingsSubmenu) {
                        bookingsSubmenu.classList.remove('hidden');
                        bookingsSubmenu.style.display = 'block';
                        bookingsSubmenu.style.maxHeight = '200px';
                        bookingsSubmenu.style.opacity = '1';
                        bookingsSubmenu.classList.remove('max-h-0', 'opacity-0');
                        bookingsSubmenu.classList.add('max-h-40', 'opacity-100');
                        if (bookingsArrow) {
                            bookingsArrow.classList.add('rotate-90');
                        }
                    }
                @endif
                
                // Auto-expand Categories submenu if on category pages
                @if(request()->routeIs('admin.booking-categories*'))
                    const categoriesSubmenu = document.getElementById('categories-submenu');
                    const categoriesArrow = document.getElementById('categories-submenu-arrow');
                    if (categoriesSubmenu) {
                        categoriesSubmenu.classList.remove('hidden');
                        categoriesSubmenu.style.display = 'block';
                        categoriesSubmenu.style.maxHeight = '200px';
                        categoriesSubmenu.style.opacity = '1';
                        categoriesSubmenu.classList.remove('max-h-0', 'opacity-0');
                        categoriesSubmenu.classList.add('max-h-40', 'opacity-100');
                        if (categoriesArrow) {
                            categoriesArrow.classList.add('rotate-90');
                        }
                    }
                @endif
                
                // Auto-expand Direct Entry submenu if on direct entry pages
                @if(request()->routeIs('admin.direct-entry*'))
                    const directEntrySubmenu = document.getElementById('direct-entry-submenu');
                    const directEntryArrow = document.getElementById('direct-entry-submenu-arrow');
                    if (directEntrySubmenu) {
                        directEntrySubmenu.classList.remove('hidden');
                        directEntrySubmenu.style.display = 'block';
                        directEntrySubmenu.style.maxHeight = '200px';
                        directEntrySubmenu.style.opacity = '1';
                        directEntrySubmenu.classList.remove('max-h-0', 'opacity-0');
                        directEntrySubmenu.classList.add('max-h-40', 'opacity-100');
                        if (directEntryArrow) {
                            directEntryArrow.classList.add('rotate-90');
                        }
                    }
                @endif
                
                // Auto-expand Reports submenu if on report pages
                @if(request()->routeIs('admin.reports*'))
                    const reportsSubmenu = document.getElementById('reports-submenu');
                    const reportsArrow = document.getElementById('reports-submenu-arrow');
                    if (reportsSubmenu) {
                        reportsSubmenu.classList.remove('hidden');
                        reportsSubmenu.style.display = 'block';
                        reportsSubmenu.style.maxHeight = '800px';
                        reportsSubmenu.style.opacity = '1';
                        reportsSubmenu.classList.remove('max-h-0', 'opacity-0');
                        reportsSubmenu.classList.add('max-h-[800px]', 'opacity-100');
                        if (reportsArrow) {
                            reportsArrow.classList.add('rotate-90');
                        }
                    }
                @endif
                
                // Auto-expand Banks submenu if on bank pages
                @if(request()->routeIs('admin.banks*'))
                    const banksSubmenu = document.getElementById('banks-submenu');
                    const banksArrow = document.getElementById('banks-submenu-arrow');
                    if (banksSubmenu) {
                        banksSubmenu.classList.remove('hidden');
                        banksSubmenu.style.display = 'block';
                        banksSubmenu.style.maxHeight = '200px';
                        banksSubmenu.style.opacity = '1';
                        banksSubmenu.classList.remove('max-h-0', 'opacity-0');
                        banksSubmenu.classList.add('max-h-40', 'opacity-100');
                        if (banksArrow) {
                            banksArrow.classList.add('rotate-90');
                        }
                    }
                @endif
                
                // Auto-expand Payments Into Bank submenu if on payment pages
                @if(request()->routeIs('admin.payments-into-bank*'))
                    const paymentsIntoBankSubmenu = document.getElementById('payments-into-bank-submenu');
                    const paymentsIntoBankArrow = document.getElementById('payments-into-bank-submenu-arrow');
                    if (paymentsIntoBankSubmenu) {
                        paymentsIntoBankSubmenu.classList.remove('hidden');
                        paymentsIntoBankSubmenu.style.display = 'block';
                        paymentsIntoBankSubmenu.style.maxHeight = '200px';
                        paymentsIntoBankSubmenu.style.opacity = '1';
                        paymentsIntoBankSubmenu.classList.remove('max-h-0', 'opacity-0');
                        paymentsIntoBankSubmenu.classList.add('max-h-40', 'opacity-100');
                        if (paymentsIntoBankArrow) {
                            paymentsIntoBankArrow.classList.add('rotate-90');
                        }
                    }
                @endif
                
                // Auto-expand Payments submenu if on payment pages
                @if(request()->routeIs('admin.payments*'))
                    const paymentsSubmenu = document.getElementById('payments-submenu');
                    const paymentsArrow = document.getElementById('payments-submenu-arrow');
                    if (paymentsSubmenu) {
                        paymentsSubmenu.classList.remove('hidden');
                        paymentsSubmenu.style.display = 'block';
                        paymentsSubmenu.style.maxHeight = '300px';
                        paymentsSubmenu.style.opacity = '1';
                        paymentsSubmenu.classList.remove('max-h-0', 'opacity-0');
                        paymentsSubmenu.classList.add('max-h-60', 'opacity-100');
                        if (paymentsArrow) {
                            paymentsArrow.classList.add('rotate-90');
                        }
                    }
                @endif
                
                // Auto-expand Payroll submenu if on payroll pages
                @if(request()->routeIs('admin.payroll*'))
                    const payrollSubmenu = document.getElementById('payroll-submenu');
                    const payrollArrow = document.getElementById('payroll-submenu-arrow');
                    if (payrollSubmenu) {
                        payrollSubmenu.classList.remove('hidden');
                        payrollSubmenu.style.display = 'block';
                        payrollSubmenu.style.maxHeight = '300px';
                        payrollSubmenu.style.opacity = '1';
                        payrollSubmenu.classList.remove('max-h-0', 'opacity-0');
                        payrollSubmenu.classList.add('max-h-60', 'opacity-100');
                        if (payrollArrow) {
                            payrollArrow.classList.add('rotate-90');
                        }
                    }
                @endif
            });
            
            // Mobile Sidebar Toggle Function
            function toggleMobileSidebar() {
                const sidebar = document.getElementById('admin-sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                
                if (sidebar && overlay) {
                    sidebar.classList.toggle('mobile-open');
                    overlay.classList.toggle('active');
                    
                    // Prevent body scroll when sidebar is open on mobile
                    if (sidebar.classList.contains('mobile-open')) {
                        document.body.style.overflow = 'hidden';
                    } else {
                        document.body.style.overflow = '';
                    }
                }
            }
            
            // Close sidebar when clicking on a nav link on mobile
            document.addEventListener('DOMContentLoaded', function() {
                const navLinks = document.querySelectorAll('.nav-link, .submenu-link');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        // Only close on mobile
                        if (window.innerWidth < 1024) {
                            toggleMobileSidebar();
                        }
                    });
                });
            });
        </script>
    </head>
    <body class="min-h-screen bg-gray-50">
        <div class="flex flex-col h-screen">
            <!-- Top Header -->
            <header class="admin-header sticky top-0 z-10">
                <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 py-4 sm:py-5">
                    <div class="flex items-center">
                        <!-- Mobile Menu Button -->
                        <button id="mobile-menu-button" onclick="toggleMobileSidebar()" class="lg:hidden mr-3 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105" style="background: var(--admin-gradient); box-shadow: 0 4px 16px rgba(255, 117, 15, 0.35);">
                                <span class="text-white font-bold text-lg sm:text-xl">H</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-1 sm:gap-1.5">
                                    <span class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 tracking-tight">Haxo</span>
                                    <span class="text-lg sm:text-xl lg:text-2xl font-bold tracking-tight" style="color: var(--admin-orange);">Shipping</span>
                                </div>
                                <p class="text-xs text-gray-500 font-medium mt-0.5 hidden sm:block">Admin Panel</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 sm:gap-4">
                        <!-- Language Selector -->
                        <div class="hidden md:flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                            <span>🇺🇸</span>
                            <span class="text-sm font-medium">English</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <!-- Globe Icon -->
                        <button class="hidden md:block p-2 rounded-lg hover:bg-gray-50 transition">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </button>
                        <!-- Notifications -->
                        <div class="relative">
                            <button id="notification-button" onclick="toggleNotificationDropdown()" class="relative p-2 rounded-lg hover:bg-gray-50 transition">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <span id="notification-badge" class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full hidden"></span>
                                <span id="notification-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center hidden"></span>
                            </button>
                            
                            <!-- Notification Dropdown -->
                            <div id="notification-dropdown" class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 hidden notification-dropdown-mobile" style="max-height: 500px; overflow-y: auto;">
                                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                                    <button onclick="markAllAsRead()" class="text-sm text-orange-600 hover:text-orange-700 font-medium">Mark all as read</button>
                                </div>
                                <div id="notification-list" class="divide-y divide-gray-100">
                                    <div class="p-4 text-center text-gray-500">
                                        <p>Loading notifications...</p>
                                    </div>
                                </div>
                                <div id="notification-empty" class="p-8 text-center text-gray-500 hidden">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    <p>No notifications</p>
                                </div>
                            </div>
                        </div>
                        <!-- To Do Button -->
                        <button class="admin-btn-primary px-3 sm:px-5 py-2 sm:py-2.5 text-xs sm:text-sm hidden sm:flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span class="hidden md:inline">To Do</span>
                        </button>
                        
                        <!-- User Profile -->
                        @if(session('admin_user_name'))
                            <div class="hidden md:flex items-center gap-2 px-3 py-2 rounded-lg bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-200">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-r from-orange-500 to-orange-400 flex items-center justify-center shadow-md">
                                    <span class="text-white text-sm font-bold">{{ substr(session('admin_user_name'), 0, 1) }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-xs font-medium text-gray-500">Logged in as</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ session('admin_user_name') }}</span>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Logout Button - Always Visible and Prominent -->
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline-block">
                            @csrf
                            <button type="submit" class="px-3 sm:px-6 py-2 sm:py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs sm:text-sm font-bold rounded-lg hover:from-red-600 hover:to-red-700 transition-all shadow-lg hover:shadow-xl flex items-center gap-1 sm:gap-2 hover:scale-105 active:scale-95 border-2 border-red-400" title="Logout from Admin Panel">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span class="hidden sm:inline">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <div class="flex flex-1 overflow-hidden">
                <!-- Sidebar Overlay (Mobile) -->
                <div id="sidebar-overlay" class="sidebar-overlay" onclick="toggleMobileSidebar()"></div>
                
                <!-- Sidebar -->
                <aside id="admin-sidebar" class="admin-sidebar w-64 lg:w-64 overflow-y-auto" style="scrollbar-width: thin; scrollbar-color: rgba(255, 117, 15, 0.2) transparent;">
                    <!-- Mobile Close Button -->
                    <div class="lg:hidden flex items-center justify-between p-4 border-b border-gray-200">
                        <span class="text-lg font-bold text-gray-900">Menu</span>
                        <button onclick="toggleMobileSidebar()" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Navigation Menu -->
                    <nav class="p-4 space-y-1">
                        <!-- Dashboard -->
                        <a href="{{ route('admin.dashboard') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl @if(request()->routeIs('admin.dashboard')) active @endif">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all" style="@if(request()->routeIs('admin.dashboard')) background: rgba(255,255,255,0.2); @else background: rgba(255, 117, 15, 0.08); @endif">
                                <svg class="w-5 h-5 @if(request()->routeIs('admin.dashboard')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                            </div>
                            <span class="font-semibold text-sm">Dashboard</span>
                        </a>

                        <!-- User Roles & Permission -->
                        <a href="{{ route('admin.roles') }}" class="nav-link flex items-center justify-between px-4 py-3 rounded-xl @if(request()->routeIs('admin.roles*')) active @endif">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all" style="@if(request()->routeIs('admin.roles*')) background: rgba(255,255,255,0.2); @else background: rgba(255, 117, 15, 0.08); @endif">
                                    <svg class="w-5 h-5 @if(request()->routeIs('admin.roles*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <span class="font-semibold text-sm">User Roles & Permission</span>
                            </div>
                            <svg class="w-5 h-5 @if(request()->routeIs('admin.roles*')) text-white @else text-gray-400 @endif transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>

                        <!-- Rate Calculator -->
                        <a href="{{ route('admin.rate-calculator') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl @if(request()->routeIs('admin.rate-calculator*')) active @endif">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all" style="@if(request()->routeIs('admin.rate-calculator*')) background: rgba(255,255,255,0.2); @else background: rgba(255, 117, 15, 0.08); @endif">
                                <svg class="w-5 h-5 @if(request()->routeIs('admin.rate-calculator*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <span class="font-semibold text-sm">Rate Calculator</span>
                        </a>

                        <!-- Search with AWB -->
                        <div class="relative nav-link @if(request()->routeIs('admin.search-with-awb*')) active @endif">
                            <div class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all duration-300" onclick="toggleSubmenu('search-awb-submenu')">
                                <div class="flex items-center gap-3 flex-1">
                                    @if(request()->routeIs('admin.search-with-awb*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.search-with-awb*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-5 h-5 @if(request()->routeIs('admin.search-with-awb*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-sm @if(request()->routeIs('admin.search-with-awb*')) text-white @else text-gray-700 @endif">Search with AWB</span>
                                </div>
                                <svg class="w-5 h-5 flex-shrink-0 @if(request()->routeIs('admin.search-with-awb*')) text-white @else text-gray-400 @endif transition-transform duration-300" id="search-awb-submenu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div id="search-awb-submenu" class="mt-2 ml-2 space-y-1.5 overflow-hidden transition-all duration-300" style="max-height: 0; opacity: 0; display: none;">
                                <a href="{{ route('admin.search-with-awb.search') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.search-with-awb.search')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.search-with-awb.search'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.search-with-awb.search')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.search-with-awb.search')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.search-with-awb.search')) text-white @else text-gray-700 @endif">Search</span>
                                </a>
                                <a href="{{ route('admin.search-with-awb.history') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.search-with-awb.history')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.search-with-awb.history'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.search-with-awb.history')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.search-with-awb.history')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.search-with-awb.history')) text-white @else text-gray-700 @endif">History</span>
                                </a>
                            </div>
                        </div>

                        <!-- Networks -->
                        <div class="relative nav-link @if(request()->routeIs('admin.networks*')) active @endif">
                            <div class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all duration-300" onclick="toggleSubmenu('networks-submenu')">
                                <div class="flex items-center gap-3 flex-1">
                                    @if(request()->routeIs('admin.networks*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.networks*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-5 h-5 @if(request()->routeIs('admin.networks*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-sm @if(request()->routeIs('admin.networks*')) text-white @else text-gray-700 @endif">Networks</span>
                                </div>
                                <svg class="w-5 h-5 flex-shrink-0 @if(request()->routeIs('admin.networks*')) text-white @else text-gray-400 @endif transition-transform duration-300" id="networks-submenu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div id="networks-submenu" class="mt-2 ml-2 space-y-1.5 overflow-hidden transition-all duration-300" style="max-height: 0; opacity: 0; display: none;">
                                <a href="{{ route('admin.networks.create') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.networks.create')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.networks.create'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.networks.create')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.networks.create')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.networks.create')) text-white @else text-gray-700 @endif">Create Network</span>
                                </a>
                                <a href="{{ route('admin.networks.all') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.networks.all') || request()->routeIs('admin.networks.edit')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.networks.all') || request()->routeIs('admin.networks.edit'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.networks.all') || request()->routeIs('admin.networks.edit')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.networks.all') || request()->routeIs('admin.networks.edit')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.networks.all') || request()->routeIs('admin.networks.edit')) text-white @else text-gray-700 @endif">All Networks</span>
                                </a>
                            </div>
                        </div>

                        <!-- Transactions -->
                        <a href="{{ route('admin.transactions.all') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl @if(request()->routeIs('admin.transactions*')) active @endif">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all" style="@if(request()->routeIs('admin.transactions*')) background: rgba(255,255,255,0.2); @else background: rgba(255, 117, 15, 0.08); @endif">
                                <svg class="w-5 h-5 @if(request()->routeIs('admin.transactions*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <span class="font-semibold text-sm">Transactions</span>
                        </a>

                        <!-- Services -->
                        <div class="relative nav-link @if(request()->routeIs('admin.services*')) active @endif">
                            <div class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all duration-300" onclick="toggleSubmenu('services-submenu')">
                                <div class="flex items-center gap-3 flex-1">
                                    @if(request()->routeIs('admin.services*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.services*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-5 h-5 @if(request()->routeIs('admin.services*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-sm @if(request()->routeIs('admin.services*')) text-white @else text-gray-700 @endif">Services</span>
                                </div>
                                <svg class="w-5 h-5 flex-shrink-0 @if(request()->routeIs('admin.services*')) text-white @else text-gray-400 @endif transition-transform duration-300" id="services-submenu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div id="services-submenu" class="mt-2 ml-2 space-y-1.5 overflow-hidden transition-all duration-300" style="max-height: 0; opacity: 0; display: none;">
                                <a href="{{ route('admin.services.create') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.services.create')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.services.create'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.services.create')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.services.create')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.services.create')) text-white @else text-gray-700 @endif">Create Service</span>
                                </a>
                                <a href="{{ route('admin.services.all') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.services.all') || request()->routeIs('admin.services.edit')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.services.all') || request()->routeIs('admin.services.edit'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.services.all') || request()->routeIs('admin.services.edit')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.services.all') || request()->routeIs('admin.services.edit')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.services.all') || request()->routeIs('admin.services.edit')) text-white @else text-gray-700 @endif">All Services</span>
                                </a>
                            </div>
                        </div>

                        <!-- Countries -->
                        <div class="relative nav-link @if(request()->routeIs('admin.countries*')) active @endif">
                            <div class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all duration-300" onclick="toggleSubmenu('countries-submenu')">
                                <div class="flex items-center gap-3 flex-1">
                                    @if(request()->routeIs('admin.countries*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.countries*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-5 h-5 @if(request()->routeIs('admin.countries*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-sm @if(request()->routeIs('admin.countries*')) text-white @else text-gray-700 @endif">Countries</span>
                                </div>
                                <svg class="w-5 h-5 flex-shrink-0 @if(request()->routeIs('admin.countries*')) text-white @else text-gray-400 @endif transition-transform duration-300" id="countries-submenu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div id="countries-submenu" class="mt-2 ml-2 space-y-1.5 overflow-hidden transition-all duration-300" style="max-height: 0; opacity: 0; display: none;">
                                <a href="{{ route('admin.countries.create') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.countries.create')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.countries.create'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.countries.create')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.countries.create')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.countries.create')) text-white @else text-gray-700 @endif">Create Country</span>
                                </a>
                                <a href="{{ route('admin.countries.all') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.countries.all') || request()->routeIs('admin.countries.edit')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.countries.all') || request()->routeIs('admin.countries.edit'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.countries.all') || request()->routeIs('admin.countries.edit')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.countries.all') || request()->routeIs('admin.countries.edit')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.countries.all') || request()->routeIs('admin.countries.edit')) text-white @else text-gray-700 @endif">All Countries</span>
                                </a>
                            </div>
                        </div>

                        <!-- Zones -->
                        <div class="relative nav-link @if(request()->routeIs('admin.zones*')) active @endif">
                            <div class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all duration-300" onclick="toggleSubmenu('zones-submenu')">
                                <div class="flex items-center gap-3 flex-1">
                                    @if(request()->routeIs('admin.zones*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.zones*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-5 h-5 @if(request()->routeIs('admin.zones*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-sm @if(request()->routeIs('admin.zones*')) text-white @else text-gray-700 @endif">Zones</span>
                                </div>
                                <svg class="w-5 h-5 flex-shrink-0 @if(request()->routeIs('admin.zones*')) text-white @else text-gray-400 @endif transition-transform duration-300" id="zones-submenu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div id="zones-submenu" class="mt-2 ml-2 space-y-1.5 overflow-hidden transition-all duration-300" style="max-height: 0; opacity: 0; display: none;">
                                <a href="{{ route('admin.zones.create') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.zones.create')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.zones.create'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.zones.create')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.zones.create')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.zones.create')) text-white @else text-gray-700 @endif">Create Zone</span>
                                </a>
                                <a href="{{ route('admin.zones.all') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.zones.all') || request()->routeIs('admin.zones.edit')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.zones.all') || request()->routeIs('admin.zones.edit'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.zones.all') || request()->routeIs('admin.zones.edit')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.zones.all') || request()->routeIs('admin.zones.edit')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.zones.all') || request()->routeIs('admin.zones.edit')) text-white @else text-gray-700 @endif">All Zones</span>
                                </a>
                            </div>
                        </div>

                        <!-- Shipping Charges -->
                        <div class="relative nav-link @if(request()->routeIs('admin.shipping-charges*')) active @endif">
                            <div class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all duration-300" onclick="toggleSubmenu('shipping-charges-submenu')">
                                <div class="flex items-center gap-3 flex-1">
                                    @if(request()->routeIs('admin.shipping-charges*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.shipping-charges*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-5 h-5 @if(request()->routeIs('admin.shipping-charges*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-sm @if(request()->routeIs('admin.shipping-charges*')) text-white @else text-gray-700 @endif">Shipping Charges</span>
                                </div>
                                <svg class="w-5 h-5 flex-shrink-0 @if(request()->routeIs('admin.shipping-charges*')) text-white @else text-gray-400 @endif transition-transform duration-300" id="shipping-charges-submenu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div id="shipping-charges-submenu" class="mt-2 ml-2 space-y-1.5 overflow-hidden transition-all duration-300" style="max-height: 0; opacity: 0; display: none;">
                                <a href="{{ route('admin.shipping-charges.create') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.shipping-charges.create')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.shipping-charges.create'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.shipping-charges.create')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.shipping-charges.create')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.shipping-charges.create')) text-white @else text-gray-700 @endif">Create Charge</span>
                                </a>
                                <a href="{{ route('admin.shipping-charges.all') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.shipping-charges.all') || request()->routeIs('admin.shipping-charges.edit')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.shipping-charges.all') || request()->routeIs('admin.shipping-charges.edit'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.shipping-charges.all') || request()->routeIs('admin.shipping-charges.edit')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.shipping-charges.all') || request()->routeIs('admin.shipping-charges.edit')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.shipping-charges.all') || request()->routeIs('admin.shipping-charges.edit')) text-white @else text-gray-700 @endif">All Charges</span>
                                </a>
                            </div>
                        </div>

                        <!-- Formulas -->
                        <div class="relative nav-link @if(request()->routeIs('admin.formulas*')) active @endif">
                            <div class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all duration-300" onclick="toggleSubmenu('formulas-submenu')">
                                <div class="flex items-center gap-3 flex-1">
                                    @if(request()->routeIs('admin.formulas*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.formulas*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-5 h-5 @if(request()->routeIs('admin.formulas*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-sm @if(request()->routeIs('admin.formulas*')) text-white @else text-gray-700 @endif">Formulas</span>
                                </div>
                                <svg class="w-5 h-5 flex-shrink-0 @if(request()->routeIs('admin.formulas*')) text-white @else text-gray-400 @endif transition-transform duration-300" id="formulas-submenu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div id="formulas-submenu" class="mt-2 ml-2 space-y-1.5 overflow-hidden transition-all duration-300" style="max-height: 0; opacity: 0; display: none;">
                                <a href="{{ route('admin.formulas.create') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.formulas.create')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.formulas.create'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.formulas.create')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.formulas.create')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.formulas.create')) text-white @else text-gray-700 @endif">Create Formula</span>
                                </a>
                                <a href="{{ route('admin.formulas.all') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.formulas.all') || request()->routeIs('admin.formulas.edit')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.formulas.all') || request()->routeIs('admin.formulas.edit'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.formulas.all') || request()->routeIs('admin.formulas.edit')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.formulas.all') || request()->routeIs('admin.formulas.edit')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.formulas.all') || request()->routeIs('admin.formulas.edit')) text-white @else text-gray-700 @endif">All Formulas</span>
                                </a>
                            </div>
                        </div>

                        <!-- AWB Upload -->
                        <div class="relative nav-link @if(request()->routeIs('admin.awb-upload*')) active @endif">
                            <div class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all duration-300" onclick="toggleSubmenu('awb-upload-submenu')">
                                <div class="flex items-center gap-3 flex-1">
                                    @if(request()->routeIs('admin.awb-upload*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.awb-upload*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-5 h-5 @if(request()->routeIs('admin.awb-upload*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-sm @if(request()->routeIs('admin.awb-upload*')) text-white @else text-gray-700 @endif">AWB Upload</span>
                                </div>
                                <svg class="w-5 h-5 flex-shrink-0 @if(request()->routeIs('admin.awb-upload*')) text-white @else text-gray-400 @endif transition-transform duration-300" id="awb-upload-submenu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div id="awb-upload-submenu" class="mt-2 ml-2 space-y-1.5 overflow-hidden transition-all duration-300" style="max-height: 0; opacity: 0; display: none;">
                                <a href="{{ route('admin.awb-upload.create') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.awb-upload.create')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.awb-upload.create'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.awb-upload.create')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.awb-upload.create')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.awb-upload.create')) text-white @else text-gray-700 @endif">Create Upload</span>
                                </a>
                                <a href="{{ route('admin.awb-upload.all') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.awb-upload.all') || request()->routeIs('admin.awb-upload.edit')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.awb-upload.all') || request()->routeIs('admin.awb-upload.edit'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.awb-upload.all') || request()->routeIs('admin.awb-upload.edit')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.awb-upload.all') || request()->routeIs('admin.awb-upload.edit')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.awb-upload.all') || request()->routeIs('admin.awb-upload.edit')) text-white @else text-gray-700 @endif">All Uploads</span>
                                </a>
                            </div>
                        </div>

                        <!-- Add Booking -->
                        <div class="relative nav-link @if(request()->routeIs('admin.bookings*')) active @endif">
                            <div class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all duration-300" onclick="toggleSubmenu('bookings-submenu')">
                                <div class="flex items-center gap-3 flex-1">
                                    @if(request()->routeIs('admin.bookings*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.bookings*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-5 h-5 @if(request()->routeIs('admin.bookings*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-sm @if(request()->routeIs('admin.bookings*')) text-white @else text-gray-700 @endif">Add Booking</span>
                                </div>
                                <svg class="w-5 h-5 flex-shrink-0 @if(request()->routeIs('admin.bookings*')) text-white @else text-gray-400 @endif transition-transform duration-300" id="bookings-submenu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div id="bookings-submenu" class="mt-2 ml-2 space-y-1.5 overflow-hidden transition-all duration-300" style="max-height: 0; opacity: 0; display: none;">
                                <a href="{{ route('admin.bookings.create') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.bookings.create')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.bookings.create'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.bookings.create')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.bookings.create')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.bookings.create')) text-white @else text-gray-700 @endif">Create Booking</span>
                                </a>
                                <a href="{{ route('admin.bookings.all') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.bookings.all') || request()->routeIs('admin.bookings.edit')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.bookings.all') || request()->routeIs('admin.bookings.edit'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.bookings.all') || request()->routeIs('admin.bookings.edit')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.bookings.all') || request()->routeIs('admin.bookings.edit')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.bookings.all') || request()->routeIs('admin.bookings.edit')) text-white @else text-gray-700 @endif">All Bookings</span>
                                </a>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="relative nav-link @if(request()->routeIs('admin.booking-categories*')) active @endif">
                            <div class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all duration-300" onclick="toggleSubmenu('categories-submenu')">
                                <div class="flex items-center gap-3 flex-1">
                                    @if(request()->routeIs('admin.booking-categories*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.booking-categories*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-5 h-5 @if(request()->routeIs('admin.booking-categories*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-sm @if(request()->routeIs('admin.booking-categories*')) text-white @else text-gray-700 @endif">Category</span>
                                </div>
                                <svg class="w-5 h-5 flex-shrink-0 @if(request()->routeIs('admin.booking-categories*')) text-white @else text-gray-400 @endif transition-transform duration-300" id="categories-submenu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div id="categories-submenu" class="mt-2 ml-2 space-y-1.5 overflow-hidden transition-all duration-300" style="max-height: 0; opacity: 0; display: none;">
                                <a href="{{ route('admin.booking-categories.create') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.booking-categories.create')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.booking-categories.create'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.booking-categories.create')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.booking-categories.create')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.booking-categories.create')) text-white @else text-gray-700 @endif">Create Category</span>
                                </a>
                                <a href="{{ route('admin.booking-categories.all') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.booking-categories.all') || request()->routeIs('admin.booking-categories.edit')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.booking-categories.all') || request()->routeIs('admin.booking-categories.edit'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.booking-categories.all') || request()->routeIs('admin.booking-categories.edit')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.booking-categories.all') || request()->routeIs('admin.booking-categories.edit')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.booking-categories.all') || request()->routeIs('admin.booking-categories.edit')) text-white @else text-gray-700 @endif">All Categories</span>
                                </a>
                            </div>
                        </div>

                        <!-- Support Tickets -->
                        <div class="relative nav-link @if(request()->routeIs('admin.support-tickets*')) active @endif">
                            <a href="{{ route('admin.support-tickets.all') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 @if(request()->routeIs('admin.support-tickets*')) active @else text-gray-700 @endif">
                                @if(request()->routeIs('admin.support-tickets*'))
                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                @endif
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.support-tickets*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                    <svg class="w-5 h-5 @if(request()->routeIs('admin.support-tickets*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                </div>
                                <span class="font-semibold text-sm @if(request()->routeIs('admin.support-tickets*')) text-white @else text-gray-700 @endif">Support Tickets</span>
                            </a>
                        </div>

                        <!-- Direct Entry -->
                        <div class="relative nav-link @if(request()->routeIs('admin.direct-entry*')) active @endif">
                            <div class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all duration-300" onclick="toggleSubmenu('direct-entry-submenu')">
                                <div class="flex items-center gap-3 flex-1">
                                    @if(request()->routeIs('admin.direct-entry*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.direct-entry*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-5 h-5 @if(request()->routeIs('admin.direct-entry*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-sm @if(request()->routeIs('admin.direct-entry*')) text-white @else text-gray-700 @endif">Direct Entry</span>
                                </div>
                                <svg class="w-5 h-5 flex-shrink-0 @if(request()->routeIs('admin.direct-entry*')) text-white @else text-gray-400 @endif transition-transform duration-300" id="direct-entry-submenu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div id="direct-entry-submenu" class="mt-2 ml-2 space-y-1.5 overflow-hidden transition-all duration-300" style="max-height: 0; opacity: 0; display: none;">
                                <a href="{{ route('admin.direct-entry.create') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.direct-entry.create')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.direct-entry.create'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.direct-entry.create')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.direct-entry.create')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.direct-entry.create')) text-white @else text-gray-700 @endif">Add Direct Entry</span>
                                </a>
                                <a href="{{ route('admin.direct-entry.all') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.direct-entry.all') || request()->routeIs('admin.direct-entry.edit')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.direct-entry.all') || request()->routeIs('admin.direct-entry.edit'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.direct-entry.all') || request()->routeIs('admin.direct-entry.edit')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.direct-entry.all') || request()->routeIs('admin.direct-entry.edit')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.direct-entry.all') || request()->routeIs('admin.direct-entry.edit')) text-white @else text-gray-700 @endif">All Entries</span>
                                </a>
                            </div>
                        </div>

                        <!-- Report -->
                        <div class="relative nav-link @if(request()->routeIs('admin.reports*')) active @endif">
                            <div class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all duration-300" onclick="toggleSubmenu('reports-submenu')">
                                <div class="flex items-center gap-3 flex-1">
                                    @if(request()->routeIs('admin.reports*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.reports*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-5 h-5 @if(request()->routeIs('admin.reports*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-sm @if(request()->routeIs('admin.reports*')) text-white @else text-gray-700 @endif">Report</span>
                                </div>
                                <svg class="w-5 h-5 flex-shrink-0 @if(request()->routeIs('admin.reports*')) text-white @else text-gray-400 @endif transition-transform duration-300" id="reports-submenu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div id="reports-submenu" class="mt-2 ml-2 space-y-1.5 overflow-y-auto transition-all duration-300" style="max-height: 0; opacity: 0; display: none;">
                                <a href="{{ route('admin.reports.zone') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.reports.zone')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.reports.zone'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.reports.zone')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.reports.zone')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.reports.zone')) text-white @else text-gray-700 @endif">Zone Report</span>
                                </a>
                                <a href="{{ route('admin.reports.formula') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.reports.formula')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.reports.formula'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.reports.formula')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.reports.formula')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.reports.formula')) text-white @else text-gray-700 @endif">Formula Report</span>
                                </a>
                                <a href="{{ route('admin.reports.shipping-charges') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.reports.shipping-charges')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.reports.shipping-charges'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.reports.shipping-charges')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.reports.shipping-charges')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.reports.shipping-charges')) text-white @else text-gray-700 @endif">Shipping Charges Report</span>
                                </a>
                                <a href="{{ route('admin.reports.booking') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.reports.booking')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.reports.booking'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.reports.booking')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.reports.booking')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.reports.booking')) text-white @else text-gray-700 @endif">Booking Report</span>
                                </a>
                                <a href="{{ route('admin.reports.payment') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.reports.payment')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.reports.payment'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.reports.payment')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.reports.payment')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.reports.payment')) text-white @else text-gray-700 @endif">Payment Report</span>
                                </a>
                                <a href="{{ route('admin.reports.transaction') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.reports.transaction')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.reports.transaction'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.reports.transaction')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.reports.transaction')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.reports.transaction')) text-white @else text-gray-700 @endif">Transaction Report</span>
                                </a>
                                <a href="{{ route('admin.reports.network') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.reports.network')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.reports.network'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.reports.network')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.reports.network')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.reports.network')) text-white @else text-gray-700 @endif">Network Report</span>
                                </a>
                                <a href="{{ route('admin.reports.service') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.reports.service')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.reports.service'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.reports.service')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.reports.service')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.reports.service')) text-white @else text-gray-700 @endif">Service Report</span>
                                </a>
                                <a href="{{ route('admin.reports.country') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.reports.country')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.reports.country'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.reports.country')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.reports.country')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.reports.country')) text-white @else text-gray-700 @endif">Country Report</span>
                                </a>
                                <a href="{{ route('admin.reports.bank') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.reports.bank')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.reports.bank'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.reports.bank')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.reports.bank')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.reports.bank')) text-white @else text-gray-700 @endif">Bank Report</span>
                                </a>
                                <a href="{{ route('admin.reports.wallet') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.reports.wallet')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.reports.wallet'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.reports.wallet')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.reports.wallet')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.reports.wallet')) text-white @else text-gray-700 @endif">Wallet Report</span>
                                </a>
                            </div>
                        </div>

                        <!-- Bank -->
                        <div class="relative nav-link @if(request()->routeIs('admin.banks*')) active @endif">
                            <div class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all duration-300" onclick="toggleSubmenu('banks-submenu')">
                                <div class="flex items-center gap-3 flex-1">
                                    @if(request()->routeIs('admin.banks*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.banks*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-5 h-5 @if(request()->routeIs('admin.banks*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-sm @if(request()->routeIs('admin.banks*')) text-white @else text-gray-700 @endif">Bank</span>
                                </div>
                                <svg class="w-5 h-5 flex-shrink-0 @if(request()->routeIs('admin.banks*')) text-white @else text-gray-400 @endif transition-transform duration-300" id="banks-submenu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div id="banks-submenu" class="mt-2 ml-2 space-y-1.5 overflow-hidden transition-all duration-300" style="max-height: 0; opacity: 0; display: none;">
                                <a href="{{ route('admin.banks.create') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.banks.create')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.banks.create'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.banks.create')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.banks.create')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.banks.create')) text-white @else text-gray-700 @endif">Create Bank</span>
                                </a>
                                <a href="{{ route('admin.banks.all') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.banks.all') || request()->routeIs('admin.banks.edit')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.banks.all') || request()->routeIs('admin.banks.edit'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.banks.all') || request()->routeIs('admin.banks.edit')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.banks.all') || request()->routeIs('admin.banks.edit')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.banks.all') || request()->routeIs('admin.banks.edit')) text-white @else text-gray-700 @endif">All Banks</span>
                                </a>
                                <a href="{{ route('admin.banks.transfer') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.banks.transfer')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.banks.transfer'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.banks.transfer')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.banks.transfer')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.banks.transfer')) text-white @else text-gray-700 @endif">Bank Transfer</span>
                                </a>
                            </div>
                        </div>

                        <!-- Add Payment Into Bank -->
                        <div class="relative nav-link @if(request()->routeIs('admin.payments-into-bank*')) active @endif">
                            <div class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all duration-300" onclick="toggleSubmenu('payments-into-bank-submenu')">
                                <div class="flex items-center gap-3 flex-1">
                                    @if(request()->routeIs('admin.payments-into-bank*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.payments-into-bank*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-5 h-5 @if(request()->routeIs('admin.payments-into-bank*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-sm @if(request()->routeIs('admin.payments-into-bank*')) text-white @else text-gray-700 @endif">Add Payment Into Bank</span>
                                </div>
                                <svg class="w-5 h-5 flex-shrink-0 @if(request()->routeIs('admin.payments-into-bank*')) text-white @else text-gray-400 @endif transition-transform duration-300" id="payments-into-bank-submenu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div id="payments-into-bank-submenu" class="mt-2 ml-2 space-y-1.5 overflow-hidden transition-all duration-300" style="max-height: 0; opacity: 0; display: none;">
                                <a href="{{ route('admin.payments-into-bank.create') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.payments-into-bank.create')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.payments-into-bank.create'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.payments-into-bank.create')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.payments-into-bank.create')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.payments-into-bank.create')) text-white @else text-gray-700 @endif">Add Payment</span>
                                </a>
                                <a href="{{ route('admin.payments-into-bank.all') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.payments-into-bank.all') || request()->routeIs('admin.payments-into-bank.edit')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.payments-into-bank.all') || request()->routeIs('admin.payments-into-bank.edit'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.payments-into-bank.all') || request()->routeIs('admin.payments-into-bank.edit')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.payments-into-bank.all') || request()->routeIs('admin.payments-into-bank.edit')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.payments-into-bank.all') || request()->routeIs('admin.payments-into-bank.edit')) text-white @else text-gray-700 @endif">All Payments</span>
                                </a>
                            </div>
                        </div>

                        <!-- Payments - Single and Bulk -->
                        <div class="relative nav-link @if(request()->routeIs('admin.payments*')) active @endif">
                            <div class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all duration-300" onclick="toggleSubmenu('payments-submenu')">
                                <div class="flex items-center gap-3 flex-1">
                                    @if(request()->routeIs('admin.payments*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.payments*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-5 h-5 @if(request()->routeIs('admin.payments*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-sm @if(request()->routeIs('admin.payments*')) text-white @else text-gray-700 @endif">Payments - Single & Bulk</span>
                                </div>
                                <svg class="w-5 h-5 flex-shrink-0 @if(request()->routeIs('admin.payments*')) text-white @else text-gray-400 @endif transition-transform duration-300" id="payments-submenu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div id="payments-submenu" class="mt-2 ml-2 space-y-1.5 overflow-hidden transition-all duration-300" style="max-height: 0; opacity: 0; display: none;">
                                <a href="{{ route('admin.payments.create') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.payments.create')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.payments.create'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.payments.create')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.payments.create')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.payments.create')) text-white @else text-gray-700 @endif">Add Payment</span>
                                </a>
                                <a href="{{ route('admin.payments.wallet') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.payments.wallet')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.payments.wallet'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.payments.wallet')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.payments.wallet')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.payments.wallet')) text-white @else text-gray-700 @endif">Manage Wallet (Bulk Update)</span>
                                </a>
                                <a href="{{ route('admin.payments.all') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.payments.all') || request()->routeIs('admin.payments.edit')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.payments.all') || request()->routeIs('admin.payments.edit'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.payments.all') || request()->routeIs('admin.payments.edit')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.payments.all') || request()->routeIs('admin.payments.edit')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.payments.all') || request()->routeIs('admin.payments.edit')) text-white @else text-gray-700 @endif">All Payments</span>
                                </a>
                                <a href="{{ route('admin.payments.gateways') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.payments.gateways*')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.payments.gateways*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.payments.gateways*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.payments.gateways*')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.payments.gateways*')) text-white @else text-gray-700 @endif">Payment Gateways</span>
                                </a>
                            </div>
                        </div>

                        <!-- Todo List -->
                        <a href="{{ route('admin.todos.index') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl @if(request()->routeIs('admin.todos*')) active @endif">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all" style="@if(request()->routeIs('admin.todos*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                <svg class="w-5 h-5 @if(request()->routeIs('admin.todos*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <span class="font-semibold text-sm @if(request()->routeIs('admin.todos*')) text-white @else text-gray-700 @endif">Todo List</span>
                        </a>

                        <!-- Settings -->
                        <div class="relative nav-link @if(request()->routeIs('admin.settings*') || request()->routeIs('admin.delivery-category*') || request()->routeIs('admin.delivery-charge*') || request()->routeIs('admin.delivery-type*') || request()->routeIs('admin.liquid-fragile*') || request()->routeIs('admin.sms-settings*') || request()->routeIs('admin.sms-send-settings*') || request()->routeIs('admin.googlemap-settings*') || request()->routeIs('admin.mail-settings*') || request()->routeIs('admin.social-login*') || request()->routeIs('admin.payment-setup*') || request()->routeIs('admin.packaging*') || request()->routeIs('admin.currency*')) active @endif">
                            <div class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all duration-300" onclick="toggleSubmenu('settings-submenu')">
                                <div class="flex items-center gap-3 flex-1">
                                    @if(request()->routeIs('admin.settings*') || request()->routeIs('admin.delivery-category*') || request()->routeIs('admin.delivery-charge*') || request()->routeIs('admin.delivery-type*') || request()->routeIs('admin.liquid-fragile*') || request()->routeIs('admin.sms-settings*') || request()->routeIs('admin.sms-send-settings*') || request()->routeIs('admin.googlemap-settings*') || request()->routeIs('admin.mail-settings*') || request()->routeIs('admin.social-login*') || request()->routeIs('admin.payment-setup*') || request()->routeIs('admin.packaging*') || request()->routeIs('admin.currency*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.settings*') || request()->routeIs('admin.delivery-category*') || request()->routeIs('admin.delivery-charge*') || request()->routeIs('admin.delivery-type*') || request()->routeIs('admin.liquid-fragile*') || request()->routeIs('admin.sms-settings*') || request()->routeIs('admin.sms-send-settings*') || request()->routeIs('admin.googlemap-settings*') || request()->routeIs('admin.mail-settings*') || request()->routeIs('admin.social-login*') || request()->routeIs('admin.payment-setup*') || request()->routeIs('admin.packaging*') || request()->routeIs('admin.currency*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-5 h-5 @if(request()->routeIs('admin.settings*') || request()->routeIs('admin.delivery-category*') || request()->routeIs('admin.delivery-charge*') || request()->routeIs('admin.delivery-type*') || request()->routeIs('admin.liquid-fragile*') || request()->routeIs('admin.sms-settings*') || request()->routeIs('admin.sms-send-settings*') || request()->routeIs('admin.googlemap-settings*') || request()->routeIs('admin.mail-settings*') || request()->routeIs('admin.social-login*') || request()->routeIs('admin.payment-setup*') || request()->routeIs('admin.packaging*') || request()->routeIs('admin.currency*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-sm @if(request()->routeIs('admin.settings*') || request()->routeIs('admin.delivery-category*') || request()->routeIs('admin.delivery-charge*') || request()->routeIs('admin.delivery-type*') || request()->routeIs('admin.liquid-fragile*') || request()->routeIs('admin.sms-settings*') || request()->routeIs('admin.sms-send-settings*') || request()->routeIs('admin.googlemap-settings*') || request()->routeIs('admin.mail-settings*') || request()->routeIs('admin.social-login*') || request()->routeIs('admin.payment-setup*') || request()->routeIs('admin.packaging*') || request()->routeIs('admin.currency*')) text-white @else text-gray-700 @endif">Setting</span>
                                </div>
                                <svg class="w-5 h-5 flex-shrink-0 @if(request()->routeIs('admin.settings*') || request()->routeIs('admin.delivery-category*') || request()->routeIs('admin.delivery-charge*') || request()->routeIs('admin.delivery-type*') || request()->routeIs('admin.liquid-fragile*') || request()->routeIs('admin.sms-settings*') || request()->routeIs('admin.sms-send-settings*') || request()->routeIs('admin.googlemap-settings*') || request()->routeIs('admin.mail-settings*') || request()->routeIs('admin.social-login*') || request()->routeIs('admin.payment-setup*') || request()->routeIs('admin.packaging*') || request()->routeIs('admin.currency*')) text-white @else text-gray-400 @endif transition-transform duration-300" id="settings-submenu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div id="settings-submenu" class="mt-2 ml-2 space-y-1.5 overflow-hidden transition-all duration-300" style="max-height: 0; opacity: 0; display: none;">
                                <a href="{{ route('admin.settings') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.settings') && !request()->routeIs('admin.settings.*')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.settings') && !request()->routeIs('admin.settings.*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.settings') && !request()->routeIs('admin.settings.*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.settings') && !request()->routeIs('admin.settings.*')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.settings') && !request()->routeIs('admin.settings.*')) text-white @else text-gray-700 @endif">General Settings</span>
                                </a>
                                <a href="{{ route('admin.delivery-category.index') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.delivery-category*')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.delivery-category*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.delivery-category*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.delivery-category*')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.delivery-category*')) text-white @else text-gray-700 @endif">Delivery Category</span>
                                </a>
                                <a href="{{ route('admin.delivery-charge.index') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.delivery-charge*')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.delivery-charge*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.delivery-charge*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.delivery-charge*')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.delivery-charge*')) text-white @else text-gray-700 @endif">Delivery Charge</span>
                                </a>
                                <a href="{{ route('admin.delivery-type.index') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.delivery-type*')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.delivery-type*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.delivery-type*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.delivery-type*')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.delivery-type*')) text-white @else text-gray-700 @endif">Delivery Type</span>
                                </a>
                                <a href="{{ route('admin.liquid-fragile.index') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.liquid-fragile*')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.liquid-fragile*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.liquid-fragile*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.liquid-fragile*')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.liquid-fragile*')) text-white @else text-gray-700 @endif">Liquid/Fragile</span>
                                </a>
                                <a href="{{ route('admin.sms-settings.index') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.sms-settings*')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.sms-settings*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.sms-settings*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.sms-settings*')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.sms-settings*')) text-white @else text-gray-700 @endif">SMS Setting</span>
                                </a>
                                <a href="{{ route('admin.sms-send-settings.index') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.sms-send-settings*')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.sms-send-settings*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.sms-send-settings*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.sms-send-settings*')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.sms-send-settings*')) text-white @else text-gray-700 @endif">SMS Send Setting</span>
                                </a>
                                <a href="{{ route('admin.notification-settings') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.notification-settings*')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.notification-settings*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.notification-settings*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.notification-settings*')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.notification-settings*')) text-white @else text-gray-700 @endif">Notification Settings</span>
                                </a>
                                <a href="{{ route('admin.googlemap-settings.index') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.googlemap-settings*')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.googlemap-settings*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.googlemap-settings*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.googlemap-settings*')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.googlemap-settings*')) text-white @else text-gray-700 @endif">GoogleMap Setting</span>
                                </a>
                                <a href="{{ route('admin.mail-settings.index') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.mail-settings*')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.mail-settings*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.mail-settings*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.mail-settings*')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.mail-settings*')) text-white @else text-gray-700 @endif">Mail Settings</span>
                                </a>
                                <a href="{{ route('admin.social-login.index') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.social-login*')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.social-login*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.social-login*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.social-login*')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.social-login*')) text-white @else text-gray-700 @endif">Social login settings</span>
                                </a>
                                <a href="{{ route('admin.payment-setup.index') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.payment-setup*')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.payment-setup*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.payment-setup*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.payment-setup*')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.payment-setup*')) text-white @else text-gray-700 @endif">Online Payment Setup</span>
                                </a>
                                <a href="{{ route('admin.packaging.index') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.packaging*')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.packaging*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.packaging*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.packaging*')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.packaging*')) text-white @else text-gray-700 @endif">Packaging</span>
                                </a>
                                <a href="{{ route('admin.currency.index') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.currency*')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.currency*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.currency*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.currency*')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.currency*')) text-white @else text-gray-700 @endif">Currency</span>
                                </a>
                            </div>
                        </div>

                        <!-- Payroll -->
                        <div class="relative nav-link @if(request()->routeIs('admin.payroll*')) active @endif">
                            <div class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all duration-300" onclick="toggleSubmenu('payroll-submenu')">
                                <div class="flex items-center gap-3 flex-1">
                                    @if(request()->routeIs('admin.payroll*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.payroll*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-5 h-5 @if(request()->routeIs('admin.payroll*')) text-white @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-sm @if(request()->routeIs('admin.payroll*')) text-white @else text-gray-700 @endif">Payroll</span>
                                </div>
                                <svg class="w-5 h-5 flex-shrink-0 @if(request()->routeIs('admin.payroll*')) text-white @else text-gray-400 @endif transition-transform duration-300" id="payroll-submenu-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div id="payroll-submenu" class="mt-2 ml-2 space-y-1.5 overflow-hidden transition-all duration-300" style="max-height: 0; opacity: 0; display: none;">
                                <a href="{{ route('admin.payroll.salary-generate.index') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.payroll.salary-generate*')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.payroll.salary-generate*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.payroll.salary-generate*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.payroll.salary-generate*')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.payroll.salary-generate*')) text-white @else text-gray-700 @endif">Salary Generate</span>
                                </a>
                                <a href="{{ route('admin.payroll.sand-bullary-generate.index') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.payroll.sand-bullary-generate*')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.payroll.sand-bullary-generate*'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.payroll.sand-bullary-generate*')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.payroll.sand-bullary-generate*')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.payroll.sand-bullary-generate*')) text-white @else text-gray-700 @endif">Sand Bullary Generate</span>
                                </a>
                                <a href="{{ route('admin.payroll.list') }}" class="submenu-link flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-all duration-300 @if(request()->routeIs('admin.payroll.list')) active @else text-gray-700 @endif">
                                    @if(request()->routeIs('admin.payroll.list'))
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-orange-400 rounded-r-lg"></div>
                                    @endif
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all flex-shrink-0" style="@if(request()->routeIs('admin.payroll.list')) background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); @else background: rgba(255, 117, 15, 0.08); @endif">
                                        <svg class="w-4.5 h-4.5 @if(request()->routeIs('admin.payroll.list')) text-white @else text-orange-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold @if(request()->routeIs('admin.payroll.list')) text-white @else text-gray-700 @endif">Payroll List</span>
                                </a>
                            </div>
                        </div>

                        <!-- Frontend Settings -->
                        <a href="{{ route('admin.frontend-settings') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.frontend-settings*') ? 'active' : '' }}">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all" style="background: rgba(255, 117, 15, 0.08);">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <span class="font-semibold text-sm">Frontend Settings</span>
                        </a>

                        <!-- Divider -->
                        <div class="my-4 border-t border-gray-200"></div>

                        <!-- Back to Site -->
                        <a href="{{ route('home') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all" style="background: rgba(255, 117, 15, 0.1);">
                                <svg class="w-5 h-5" style="color: var(--admin-orange);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                            </div>
                            <span class="font-semibold text-sm">Back to Site</span>
                        </a>
                    </nav>
                </aside>

                <!-- Main Content -->
                <main class="flex-1 overflow-y-auto" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);">
                    <div class="p-4 sm:p-6 lg:p-8">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
        
        <!-- Notification JavaScript -->
        <script>
            let notificationPollInterval;
            let unreadCount = 0;
            
            // Toggle notification dropdown
            function toggleNotificationDropdown() {
                const dropdown = document.getElementById('notification-dropdown');
                dropdown.classList.toggle('hidden');
                
                if (!dropdown.classList.contains('hidden')) {
                    loadNotifications();
                }
            }
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const button = document.getElementById('notification-button');
                const dropdown = document.getElementById('notification-dropdown');
                
                if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            });
            
            // Load notifications
            function loadNotifications() {
                fetch('{{ route("admin.notifications.index") }}')
                    .then(response => response.json())
                    .then(data => {
                        unreadCount = data.unread_count;
                        updateNotificationBadge();
                        renderNotifications(data.notifications);
                    })
                    .catch(error => {
                        console.error('Error loading notifications:', error);
                    });
            }
            
            // Render notifications
            function renderNotifications(notifications) {
                const list = document.getElementById('notification-list');
                const empty = document.getElementById('notification-empty');
                
                if (notifications.length === 0) {
                    list.innerHTML = '';
                    list.classList.add('hidden');
                    empty.classList.remove('hidden');
                    return;
                }
                
                empty.classList.add('hidden');
                list.classList.remove('hidden');
                
                list.innerHTML = notifications.map(notification => {
                    const timeAgo = getTimeAgo(notification.created_at);
                    const readClass = notification.read ? 'bg-gray-50' : 'bg-blue-50';
                    const readIcon = notification.read ? '' : '<div class="w-2 h-2 bg-blue-500 rounded-full"></div>';
                    
                    return `
                        <div class="p-4 hover:bg-gray-50 transition cursor-pointer ${readClass}" onclick="markNotificationAsRead(${notification.id})">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-1">
                                    ${getNotificationIcon(notification.type)}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="text-sm font-semibold text-gray-900">${escapeHtml(notification.title)}</p>
                                        ${readIcon}
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">${escapeHtml(notification.message)}</p>
                                    <p class="text-xs text-gray-400 mt-2">${timeAgo}</p>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }
            
            // Get notification icon based on type
            function getNotificationIcon(type) {
                const icons = {
                    'user_login': '<svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
                    'role_assigned': '<svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
                    'order_updated': '<svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
                    'default': '<svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>'
                };
                return icons[type] || icons['default'];
            }
            
            // Update notification badge
            function updateNotificationBadge() {
                const badge = document.getElementById('notification-badge');
                const count = document.getElementById('notification-count');
                
                if (unreadCount > 0) {
                    if (unreadCount > 9) {
                        badge.classList.remove('hidden');
                        count.classList.add('hidden');
                    } else {
                        badge.classList.add('hidden');
                        count.classList.remove('hidden');
                        count.textContent = unreadCount;
                    }
                } else {
                    badge.classList.add('hidden');
                    count.classList.add('hidden');
                }
            }
            
            // Mark notification as read
            function markNotificationAsRead(id) {
                fetch(`{{ route("admin.notifications.read", ":id") }}`.replace(':id', id), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    unreadCount = data.unread_count;
                    updateNotificationBadge();
                    loadNotifications();
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                });
            }
            
            // Mark all notifications as read
            function markAllAsRead() {
                fetch('{{ route("admin.notifications.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    unreadCount = 0;
                    updateNotificationBadge();
                    loadNotifications();
                })
                .catch(error => {
                    console.error('Error marking all notifications as read:', error);
                });
            }
            
            // Get time ago
            function getTimeAgo(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diffInSeconds = Math.floor((now - date) / 1000);
                
                if (diffInSeconds < 60) return 'Just now';
                if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' minutes ago';
                if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' hours ago';
                if (diffInSeconds < 604800) return Math.floor(diffInSeconds / 86400) + ' days ago';
                return date.toLocaleDateString();
            }
            
            // Escape HTML
            function escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, m => map[m]);
            }
            
            // Poll for new notifications with dynamic interval
            function triggerTodoReminders() {
                fetch('{{ route("admin.todos.poll-reminders") }}')
                    .then(() => {})
                    .catch(error => console.error('Error triggering todo reminders:', error));
            }

            function startNotificationPolling() {
                loadNotifications();
                triggerTodoReminders();
                
                // Get polling interval from settings (default 30 seconds)
                let pollingInterval = {{ \App\Models\NotificationSetting::getPollingInterval() }} * 1000;
                
                notificationPollInterval = setInterval(function() {
                    triggerTodoReminders();
                    fetch('{{ route("admin.notifications.unread-count") }}')
                        .then(response => response.json())
                        .then(data => {
                            if (data.unread_count !== unreadCount) {
                                unreadCount = data.unread_count;
                                updateNotificationBadge();
                                
                                // Check if popup is enabled for notifications
                                fetch('{{ route("admin.notifications.index") }}')
                                    .then(response => response.json())
                                    .then(notifData => {
                                        // Check if we should show popup based on latest notification
                                        if (notifData.notifications && notifData.notifications.length > 0) {
                                            const latestNotification = notifData.notifications[0];
                                            // Check if popup should be shown (we'll check settings dynamically)
                                            if (unreadCount > 0 && shouldShowPopup(latestNotification.type)) {
                                                showNotificationPopup();
                                            }
                                        }
                                    })
                                    .catch(error => console.error('Error checking notification settings:', error));
                            }
                        })
                        .catch(error => {
                            console.error('Error checking notifications:', error);
                        });
                }, pollingInterval);
            }
            
            // Check if popup should be shown for notification type
            function shouldShowPopup(type) {
                // This will be checked server-side, but for now default to true
                // In a real implementation, you'd fetch this from the notification settings
                return true;
            }
            
            // Show notification popup
            function showNotificationPopup() {
                // Create a temporary popup notification
                const popup = document.createElement('div');
                popup.className = 'fixed top-4 right-4 bg-white rounded-lg shadow-xl border border-gray-200 p-4 z-50 max-w-sm';
                popup.innerHTML = `
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-blue-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">New Notification</p>
                            <p class="text-xs text-gray-600 mt-1">You have ${unreadCount} unread notification${unreadCount > 1 ? 's' : ''}</p>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                `;
                
                document.body.appendChild(popup);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (popup.parentElement) {
                        popup.remove();
                    }
                }, 5000);
            }
            
            // Initialize notification polling when page loads
            document.addEventListener('DOMContentLoaded', function() {
                startNotificationPolling();
            });
        </script>
    </body>
</html>

