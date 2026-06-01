<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\MasterPhoto;
use App\Models\MasterProfile;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $profile = auth()->user()->masterProfile()->with(['categories', 'photos'])->firstOrCreate(
            ['user_id' => auth()->id()],
            ['city' => '', 'years_experience' => 0]
        );
        $categories = ServiceCategory::all();

        return view('cabinet.profile', compact('profile', 'categories'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'bio' => ['nullable', 'string', 'max:3000'],
            'city' => ['required', 'string', 'max:100'],
            'years_experience' => ['required', 'integer', 'min:0', 'max:60'],
            'is_available' => ['nullable', 'boolean'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['exists:service_categories,id'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ], [
            'city.required' => 'Місто обов\'язкове.',
            'years_experience.required' => 'Досвід обов\'язковий.',
            'categories.required' => 'Оберіть хоча б одну категорію.',
        ]);

        $profile = $user->masterProfile ?? MasterProfile::create(['user_id' => $user->id, 'city' => '']);

        if ($request->hasFile('photo')) {
            if ($profile->photo_path) {
                Storage::disk('public')->delete($profile->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('masters', 'public');
        }

        $profile->update([
            'bio' => $validated['bio'] ?? null,
            'city' => $validated['city'],
            'years_experience' => $validated['years_experience'],
            'is_available' => $request->boolean('is_available'),
            'photo_path' => $validated['photo_path'] ?? $profile->photo_path,
        ]);

        $profile->categories()->sync($validated['categories']);

        return back()->with('success', 'Профіль оновлено.');
    }

    public function storePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ], [
            'photo.required' => 'Фото обов\'язкове.',
            'photo.image' => 'Файл має бути зображенням.',
            'photo.max' => 'Розмір файлу не може перевищувати 2 МБ.',
        ]);

        $profile = auth()->user()->masterProfile;

        $path = $request->file('photo')->store('gallery', 'public');
        MasterPhoto::create([
            'master_profile_id' => $profile->id,
            'photo_path' => $path,
            'sort_order' => MasterPhoto::where('master_profile_id', $profile->id)->max('sort_order') + 1,
        ]);

        return back()->with('success', 'Фото додано.');
    }

    public function deletePhoto(MasterPhoto $photo): RedirectResponse
    {
        abort_unless($photo->masterProfile->user_id === auth()->id(), 403);

        Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();

        return back()->with('success', 'Фото видалено.');
    }
}
