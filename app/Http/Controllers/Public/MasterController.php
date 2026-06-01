<?php

namespace App\Http\Controllers\Public;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\MasterProfile;
use App\Models\Review;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterController extends Controller
{
    public function index(Request $request): View
    {
        $query = MasterProfile::with(['user', 'categories'])
            ->whereHas('user', fn ($q) => $q->where('is_active', true)->where('role', Role::Master->value));

        if ($request->filled('category')) {
            $query->whereHas('categories', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%'.$request->city.'%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('services', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $masters = $query->get();

        $sort = $request->input('sort', 'rating');
        $sorted = match ($sort) {
            'price_asc' => $masters->sortBy(fn ($m) => $m->minServicePrice() ?? PHP_INT_MAX),
            'price_desc' => $masters->sortByDesc(fn ($m) => $m->minServicePrice() ?? 0),
            default => $masters->sortByDesc(fn ($m) => $m->averageRating()),
        };
        $masters = $sorted->values();

        $perPage = 12;
        $page = $request->input('page', 1);
        $total = $masters->count();
        $paginatedMasters = $masters->forPage($page, $perPage);

        $categories = ServiceCategory::all();
        $cities = MasterProfile::select('city')->distinct()->pluck('city');

        return view('public.masters.index', compact('paginatedMasters', 'categories', 'cities', 'total', 'perPage', 'page', 'sort'));
    }

    public function show(User $master): View
    {
        abort_unless(
            $master->role === Role::Master && $master->is_active && $master->masterProfile,
            404
        );

        $profile = $master->masterProfile()->with(['categories', 'photos', 'services.category'])->first();
        $reviews = Review::with('client')
            ->where('master_id', $master->id)
            ->latest()
            ->paginate(10);

        $isFavorite = auth()->check()
            ? Favorite::where('client_id', auth()->id())->where('master_id', $master->id)->exists()
            : false;

        return view('public.masters.show', compact('master', 'profile', 'reviews', 'isFavorite'));
    }
}
