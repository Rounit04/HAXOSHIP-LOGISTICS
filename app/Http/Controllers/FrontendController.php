<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\FrontendSetting;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    /**
     * Display the landing page.
     */
    public function home()
    {
        $settings = FrontendSetting::getSettings();
        $blogs = Blog::where('status', 'published')->latest()->take(3)->get();

        return view('home', compact('settings', 'blogs'));
    }

    /**
     * Display all published blogs.
     */
    public function blogs()
    {
        $blogs = Blog::where('status', 'published')->latest()->get();

        return view('blogs', compact('blogs'));
    }

    /**
     * Display the contact page with settings.
     */
    public function contact()
    {
        $settings = FrontendSetting::getSettings();

        return view('contact', compact('settings'));
    }
}


