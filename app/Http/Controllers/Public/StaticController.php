<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class StaticController extends Controller
{
    public function about(): View
    {
        return view('public.about');
    }

    public function faq(): View
    {
        return view('public.faq');
    }

    public function contacts(): View
    {
        return view('public.contacts');
    }
}
