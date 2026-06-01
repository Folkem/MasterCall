<?php

namespace App\Http\Controllers;

use App\Models\MasterProfile;
use App\Models\ServiceCategory;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = ServiceCategory::withCount('masters')->get();

        $topMasters = MasterProfile::with(['user', 'categories'])
            ->whereHas('user', fn ($q) => $q->where('is_active', true))
            ->where('is_available', true)
            ->get()
            ->sortByDesc(fn ($mp) => $mp->averageRating())
            ->take(4)
            ->values();

        return view('public.home', compact('categories', 'topMasters'));
    }
}
