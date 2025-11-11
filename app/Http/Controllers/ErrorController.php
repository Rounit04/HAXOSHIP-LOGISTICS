<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class ErrorController extends Controller
{
    /**
     * Render the 404 page.
     */
    public function notFound(): Response
    {
        return response()->view('errors.404', [], 404);
    }
}


