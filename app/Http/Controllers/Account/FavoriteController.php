<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(): View
    {
        $favorites = Favorite::with(['master', 'master.masterProfile.categories'])
            ->where('client_id', auth()->id())
            ->get();

        return view('account.favorites', compact('favorites'));
    }

    public function toggle(User $master): RedirectResponse|JsonResponse
    {
        $existing = Favorite::where('client_id', auth()->id())
            ->where('master_id', $master->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'Майстра видалено з обраних.';
            $isFavorite = false;
        } else {
            Favorite::create(['client_id' => auth()->id(), 'master_id' => $master->id]);
            $message = 'Майстра додано до обраних.';
            $isFavorite = true;
        }

        if (request()->wantsJson()) {
            return response()->json(['is_favorite' => $isFavorite]);
        }

        return back()->with('success', $message);
    }
}
