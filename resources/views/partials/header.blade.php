<header class="border-b bg-white/90 backdrop-blur">
    @php
        $settings = \App\Models\FrontendSetting::getSettings();
        use Illuminate\Support\Facades\Storage;
    @endphp
    <div class="container mx-auto px-4 max-w-7xl py-4">
        <div class="flex items-center justify-between">
            <!-- Left Section: Logo and Navigation -->
            <div class="flex items-center gap-10">
                <!-- Logo - Smaller Size -->
                <a href="{{ route('home') }}" class="text-xl font-extrabold tracking-tight flex items-center gap-1.5 flex-shrink-0">
                    @if($settings->logo && Storage::disk('public')->exists($settings->logo))
                        <img src="{{ Storage::url($settings->logo) }}" alt="Logo" class="h-8 w-auto">
                    @else
                        <span class="text-xl font-extrabold" style="color: #1e3a8a;">Haxo<span style="color: {{ $settings->primary_color ?? '#FF750F' }};">Shipping</span></span>
                    @endif
                </a>
                
                <!-- Navigation Menu - Always Visible -->
                <nav class="flex items-center gap-6 text-sm font-medium">
                    <a class="transition-colors hover:font-semibold whitespace-nowrap {{ request()->routeIs('home') ? 'text-orange-600 font-semibold' : '' }}" 
                       href="{{ route('home') }}" 
                       style="{{ !request()->routeIs('home') ? 'color: ' . ($settings->text_color ?? '#1b1b18') . ';' : '' }}"
                       onmouseover="{{ !request()->routeIs('home') ? "this.style.color='" . ($settings->primary_color ?? '#FF750F') . "'" : '' }}" 
                       onmouseout="{{ !request()->routeIs('home') ? "this.style.color='" . ($settings->text_color ?? '#1b1b18') . "'" : '' }}">Home</a>
                    <a class="transition-colors hover:font-semibold whitespace-nowrap {{ request()->routeIs('pricing') ? 'text-orange-600 font-semibold' : '' }}" 
                       href="{{ route('pricing') }}" 
                       style="{{ !request()->routeIs('pricing') ? 'color: ' . ($settings->text_color ?? '#1b1b18') . ';' : '' }}"
                       onmouseover="{{ !request()->routeIs('pricing') ? "this.style.color='" . ($settings->primary_color ?? '#FF750F') . "'" : '' }}" 
                       onmouseout="{{ !request()->routeIs('pricing') ? "this.style.color='" . ($settings->text_color ?? '#1b1b18') . "'" : '' }}">Pricing</a>
                    <a class="transition-colors hover:font-semibold whitespace-nowrap {{ request()->routeIs('tracking') ? 'text-orange-600 font-semibold' : '' }}" 
                       href="{{ route('tracking') }}" 
                       style="{{ !request()->routeIs('tracking') ? 'color: ' . ($settings->text_color ?? '#1b1b18') . ';' : '' }}"
                       onmouseover="{{ !request()->routeIs('tracking') ? "this.style.color='" . ($settings->primary_color ?? '#FF750F') . "'" : '' }}" 
                       onmouseout="{{ !request()->routeIs('tracking') ? "this.style.color='" . ($settings->text_color ?? '#1b1b18') . "'" : '' }}">Tracking</a>
                    <a class="transition-colors hover:font-semibold whitespace-nowrap {{ request()->routeIs('blogs') ? 'text-orange-600 font-semibold' : '' }}" 
                       href="{{ route('blogs') }}" 
                       style="{{ !request()->routeIs('blogs') ? 'color: ' . ($settings->text_color ?? '#1b1b18') . ';' : '' }}"
                       onmouseover="{{ !request()->routeIs('blogs') ? "this.style.color='" . ($settings->primary_color ?? '#FF750F') . "'" : '' }}" 
                       onmouseout="{{ !request()->routeIs('blogs') ? "this.style.color='" . ($settings->text_color ?? '#1b1b18') . "'" : '' }}">Blogs</a>
                    <a class="transition-colors hover:font-semibold whitespace-nowrap {{ request()->routeIs('about') ? 'text-orange-600 font-semibold' : '' }}" 
                       href="{{ route('about') }}" 
                       style="{{ !request()->routeIs('about') ? 'color: ' . ($settings->text_color ?? '#1b1b18') . ';' : '' }}"
                       onmouseover="{{ !request()->routeIs('about') ? "this.style.color='" . ($settings->primary_color ?? '#FF750F') . "'" : '' }}" 
                       onmouseout="{{ !request()->routeIs('about') ? "this.style.color='" . ($settings->text_color ?? '#1b1b18') . "'" : '' }}">About</a>
                    <a class="transition-colors hover:font-semibold whitespace-nowrap {{ request()->routeIs('contact') ? 'text-orange-600 font-semibold' : '' }}" 
                       href="{{ route('contact') }}" 
                       style="{{ !request()->routeIs('contact') ? 'color: ' . ($settings->text_color ?? '#1b1b18') . ';' : '' }}"
                       onmouseover="{{ !request()->routeIs('contact') ? "this.style.color='" . ($settings->primary_color ?? '#FF750F') . "'" : '' }}" 
                       onmouseout="{{ !request()->routeIs('contact') ? "this.style.color='" . ($settings->text_color ?? '#1b1b18') . "'" : '' }}">Contact</a>
                </nav>
            </div>
            
            <!-- Right Section: Profile/Logout or Login/Register -->
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-sm font-medium transition-colors" style="color: {{ $settings->text_color ?? '#1b1b18' }};" onmouseover="this.style.color='{{ $settings->primary_color ?? '#FF750F' }}'" onmouseout="this.style.color='{{ $settings->text_color ?? '#1b1b18' }}'">Profile</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline-block">
                        @csrf
                        <button type="submit" class="px-5 py-2 text-sm font-semibold rounded-lg transition-all shadow-sm hover:shadow-md hover:opacity-90" style="background: {{ $settings->primary_color ?? '#FF750F' }}; color: white;">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium transition-colors" style="color: {{ $settings->text_color ?? '#1b1b18' }};" onmouseover="this.style.color='{{ $settings->primary_color ?? '#FF750F' }}'" onmouseout="this.style.color='{{ $settings->text_color ?? '#1b1b18' }}'">Login</a>
                    <a href="{{ route('register') }}" class="px-5 py-2 text-sm font-semibold rounded-lg transition-all shadow-sm hover:shadow-md hover:opacity-90" style="background: {{ $settings->primary_color ?? '#FF750F' }}; color: white;">Register</a>
                @endauth
            </div>
        </div>
    </div>
</header>


