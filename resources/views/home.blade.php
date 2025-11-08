@extends('layouts.app')

@section('title', 'Hassle Free Fastest Delivery - Haxo Shipping')

@section('content')
    @php
        use Illuminate\Support\Facades\Storage;
    @endphp
    <section class="section section-muted">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="subheading">We Provide</div>
                    <h1 class="heading-hero mt-2" style="color: {{ $settings->text_color ?? '#1b1b18' }};">{!! $settings->hero_title ?? 'Hassle Free<br/>Fastest Delivery' !!}</h1>
                    <p class="mt-4 muted max-w-xl" style="color: {{ $settings->text_color ?? '#1b1b18' }};">{{ $settings->hero_subtitle ?? 'We Committed to delivery - Make easy Efficient and quality delivery.' }}</p>
                    <a href="{{ route('tracking') }}" class="mt-8 btn btn-primary" style="background: {{ $settings->primary_color ?? '#FF750F' }}; color: white;">{{ $settings->hero_button_text ?? 'Track Now' }}</a>
                </div>
                <div class="h-80 bg-gray-200 rounded overflow-hidden">
                    @if($settings->banner && Storage::disk('public')->exists($settings->banner))
                        <img src="{{ Storage::url($settings->banner) }}" alt="Banner" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="container mx-auto px-4 max-w-7xl section">
        <div class="services-title-wrapper">
            <h2 class="services-heading">Our Services</h2>
            <div class="services-underline"></div>
        </div>
        <div class="service-grid">
            <!-- E-Commerce delivery -->
            <div class="service-card">
                <div class="service-icon-wrapper">
                    <div class="service-icon-diamond">
                        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" class="service-icon-svg">
                            <path d="M3 7h10v8H3z"/>
                            <path d="M13 10h5l3 3v2h-8"/>
                            <circle cx="7" cy="17" r="1.5"/>
                            <circle cx="17" cy="17" r="1.5"/>
                            <rect x="13" y="8" width="3" height="3" rx="0.5"/>
                            <path d="M14.5 9l1 1" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
                <h3 class="service-title">E-Commerce delivery</h3>
                <p class="service-description">Fast, reliable delivery solutions designed for online stores to ensure smooth order fulfillment and on-time customer satisfaction.</p>
            </div>

            <!-- Pick & Drop -->
            <div class="service-card">
                <div class="service-icon-wrapper">
                    <div class="service-icon-diamond">
                        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" class="service-icon-svg">
                            <circle cx="9" cy="9" r="3"/>
                            <circle cx="15" cy="15" r="3"/>
                            <path d="M9 9l6 6" stroke-linecap="round"/>
                            <path d="M12 6v3m0 3v3" stroke-linecap="round"/>
                            <path d="M15 12l-3-3" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
                <h3 class="service-title">Pick & Drop</h3>
                <p class="service-description">Flexible pickup and drop services that make parcel movement easy, efficient, and trackable from start to finish.</p>
            </div>

            <!-- Packaging -->
            <div class="service-card">
                <div class="service-icon-wrapper">
                    <div class="service-icon-diamond">
                        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" class="service-icon-svg">
                            <rect x="5" y="7" width="14" height="10" rx="2"/>
                            <path d="M9 7v-2h6v2"/>
                            <circle cx="18" cy="9" r="2" fill="white"/>
                            <path d="M18 8v2" stroke="white" stroke-width="1"/>
                        </svg>
                    </div>
                </div>
                <h3 class="service-title">Packaging</h3>
                <p class="service-description">Secure, professional packaging that keeps every product safe, protected, and presentable throughout transit.</p>
            </div>

            <!-- Warehousing -->
            <div class="service-card">
                <div class="service-icon-wrapper">
                    <div class="service-icon-diamond">
                        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" class="service-icon-svg">
                            <path d="M4 10h16v9H4z"/>
                            <path d="M8 10V7h8v3"/>
                            <rect x="7" y="13" width="3" height="3" rx="0.5"/>
                            <rect x="14" y="13" width="3" height="3" rx="0.5"/>
                        </svg>
                    </div>
                </div>
                <h3 class="service-title">Warehousing</h3>
                <p class="service-description">Smart storage and inventory management with real-time tracking to handle bulk goods efficiently and cost-effectively.</p>
            </div>
        </div>
    </section>

    <section class="section section-white">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="why-haxo-title-wrapper">
                <h2 class="why-haxo-heading">Why Haxo Shipping</h2>
                <div class="why-haxo-underline"></div>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="feature-card feature-card-highlight">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Timely Delivery</h3>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M8 7h12M16 3l4 4-4 4M4 17h12M8 21l-4-4 4-4"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Limitless Pickup</h3>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                            <line x1="1" y1="10" x2="23" y2="10"/>
                            <path d="M6 16h.01M10 16h.01M14 16h.01M18 16h.01"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Cash on delivery (COD)</h3>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"/>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Get Payment Any Time</h3>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <polyline points="9 12 11 14 15 10"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Secure Handling</h3>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="20" x2="18" y2="10"/>
                            <line x1="12" y1="20" x2="12" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Live Tracking Update</h3>
                </div>
            </div>
        </div>
    </section>

    <section class="container mx-auto px-4 max-w-7xl section">
        <!-- Title Section with Decorative Lines -->
        <div class="pricing-title-section">
            <h2 class="pricing-main-title">Haxo Shipping Pricing</h2>
            <div class="pricing-title-decoration">
                <div class="decoration-line"></div>
                <div class="decoration-line"></div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="pricing-tabs-container">
            <div class="pricing-tabs-wrapper">
                <button class="pricing-tab active" data-tab="same-day-home">
                    Same Day
                </button>
                <button class="pricing-tab" data-tab="next-day-home">
                    Next Day
                </button>
                <button class="pricing-tab" data-tab="sub-city-home">
                    Sub City
                </button>
                <button class="pricing-tab" data-tab="outside-city-home">
                    Outside City
                </button>
            </div>
            <div class="pricing-tabs-underline"></div>
        </div>

        <!-- Pricing Cards Grid -->
        <div class="pricing-cards-container">
            <div class="pricing-tab-content active" id="same-day-home-content">
                <div class="pricing-grid">
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 30 (KG)</div>
                        <div class="pricing-card-price">{{ currency(50.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 2 (KG)</div>
                        <div class="pricing-card-price">{{ currency(90.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 3 (KG)</div>
                        <div class="pricing-card-price">{{ currency(130.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 4 (KG)</div>
                        <div class="pricing-card-price">{{ currency(170.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 5 (KG)</div>
                        <div class="pricing-card-price">{{ currency(210.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 6 (KG)</div>
                        <div class="pricing-card-price">{{ currency(250.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 7 (KG)</div>
                        <div class="pricing-card-price">{{ currency(290.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 8 (KG)</div>
                        <div class="pricing-card-price">{{ currency(340.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 9 (KG)</div>
                        <div class="pricing-card-price">{{ currency(380.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 10 (KG)</div>
                        <div class="pricing-card-price">{{ currency(420.00) }}</div>
                    </div>
                </div>
            </div>

            <div class="pricing-tab-content" id="next-day-home-content">
                <div class="pricing-grid">
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 30 (KG)</div>
                        <div class="pricing-card-price">{{ currency(50.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 2 (KG)</div>
                        <div class="pricing-card-price">{{ currency(90.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 3 (KG)</div>
                        <div class="pricing-card-price">{{ currency(130.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 4 (KG)</div>
                        <div class="pricing-card-price">{{ currency(170.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 5 (KG)</div>
                        <div class="pricing-card-price">{{ currency(210.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 6 (KG)</div>
                        <div class="pricing-card-price">{{ currency(250.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 7 (KG)</div>
                        <div class="pricing-card-price">{{ currency(290.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 8 (KG)</div>
                        <div class="pricing-card-price">{{ currency(340.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 9 (KG)</div>
                        <div class="pricing-card-price">{{ currency(380.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 10 (KG)</div>
                        <div class="pricing-card-price">{{ currency(420.00) }}</div>
                    </div>
                </div>
            </div>

            <div class="pricing-tab-content" id="sub-city-home-content">
                <div class="pricing-grid">
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 30 (KG)</div>
                        <div class="pricing-card-price">{{ currency(50.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 2 (KG)</div>
                        <div class="pricing-card-price">{{ currency(90.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 3 (KG)</div>
                        <div class="pricing-card-price">{{ currency(130.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 4 (KG)</div>
                        <div class="pricing-card-price">{{ currency(170.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 5 (KG)</div>
                        <div class="pricing-card-price">{{ currency(210.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 6 (KG)</div>
                        <div class="pricing-card-price">{{ currency(250.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 7 (KG)</div>
                        <div class="pricing-card-price">{{ currency(290.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 8 (KG)</div>
                        <div class="pricing-card-price">{{ currency(340.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 9 (KG)</div>
                        <div class="pricing-card-price">{{ currency(380.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 10 (KG)</div>
                        <div class="pricing-card-price">{{ currency(420.00) }}</div>
                    </div>
                </div>
            </div>

            <div class="pricing-tab-content" id="outside-city-home-content">
                <div class="pricing-grid">
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 30 (KG)</div>
                        <div class="pricing-card-price">{{ currency(50.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 2 (KG)</div>
                        <div class="pricing-card-price">{{ currency(90.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 3 (KG)</div>
                        <div class="pricing-card-price">{{ currency(130.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 4 (KG)</div>
                        <div class="pricing-card-price">{{ currency(170.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 5 (KG)</div>
                        <div class="pricing-card-price">{{ currency(210.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 6 (KG)</div>
                        <div class="pricing-card-price">{{ currency(250.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 7 (KG)</div>
                        <div class="pricing-card-price">{{ currency(290.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 8 (KG)</div>
                        <div class="pricing-card-price">{{ currency(340.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 9 (KG)</div>
                        <div class="pricing-card-price">{{ currency(380.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 10 (KG)</div>
                        <div class="pricing-card-price">{{ currency(420.00) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const homeTabs = document.querySelectorAll('.pricing-tab[data-tab*="-home"]');
        const homeContents = document.querySelectorAll('.pricing-tab-content[id*="-home-content"]');

        homeTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');
                
                // Remove active class from all tabs and contents
                homeTabs.forEach(t => t.classList.remove('active'));
                homeContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked tab and corresponding content
                this.classList.add('active');
                document.getElementById(targetTab + '-content').classList.add('active');
            });
        });
    });
    </script>

    <section class="section section-muted">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="grid md:grid-cols-4 gap-6">
                <div class="stat-card">
                    <div class="stat-number">7K+</div>
                    <div class="stat-label">Branches</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">50M+</div>
                    <div class="stat-label">Parcel Delivered</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">4L+</div>
                    <div class="stat-label">Happy Merchant</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">700 +</div>
                    <div class="stat-label">Positive Reviews</div>
                </div>
            </div>
        </div>
    </section>

    <section class="container mx-auto px-4 max-w-7xl section section-white">
        <div class="blogs-title-wrapper">
            <h2 class="blogs-heading">Blogs</h2>
            <div class="blogs-underline"></div>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @forelse($blogs as $blog)
            <article class="blog-card">
                @if($blog->image_url)
                    <div class="blog-image-wrapper">
                        <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="blog-featured-image">
                    </div>
                @endif
                <div class="blog-content">
                    <h3 class="blog-title">{{ $blog->title }}</h3>
                    <div class="blog-meta">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ $blog->author }}
                        </span>
                        <span>·</span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            {{ $blog->views }}
                        </span>
                        <span>·</span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $blog->formatted_date }}
                        </span>
                    </div>
                </div>
            </article>
            @empty
            <div class="col-span-3 text-center py-12">
                <p class="text-gray-500">No blog posts available yet.</p>
            </div>
            @endforelse
        </div>
    </section>
@endsection



