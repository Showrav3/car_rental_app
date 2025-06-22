<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AboutController extends Controller
{
    public function about()
    {
        return Inertia::render('Frontend/AboutPage');
    }
}
