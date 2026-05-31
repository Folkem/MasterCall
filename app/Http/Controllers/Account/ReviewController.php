<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct(private ReviewService $reviewService) {}

    public function create(Request $request): View
    {
        abort_if(! $request->filled('master'), 404);
        $master = User::findOrFail($request->master);
        abort_unless($master->isMaster() && $master->is_active, 404);
        $existing = Review::where('client_id', auth()->id())->where('master_id', $master->id)->first();

        return view('account.reviews.create', compact('master', 'existing'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'master_id' => ['required', 'exists:users,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ], [
            'rating.required' => 'Оцінка обов\'язкова.',
            'rating.min' => 'Оцінка від 1 до 5.',
            'rating.max' => 'Оцінка від 1 до 5.',
        ]);

        $master = User::findOrFail($request->master_id);
        abort_unless($master->isMaster(), 403);

        try {
            $this->reviewService->store(auth()->user(), $master, (int) $request->rating, $request->comment);
        } catch (ValidationException $e) {
            return back()->withInput()->with('error', collect($e->errors())->flatten()->first());
        }

        return redirect()->route('masters.show', $master)->with('success', 'Відгук збережено.');
    }
}
