@extends('layouts.app')
@section('title', 'Blogs - Haxo Shipping')
@section('content')
<div class="container mx-auto px-4 max-w-7xl section section-white">
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
</div>
@endsection


