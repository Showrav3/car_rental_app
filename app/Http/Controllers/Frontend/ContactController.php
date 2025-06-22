<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function contact()
    {
        return Inertia::render('Frontend/ContactPage');
    }
}
