<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\MasterPhoto;
use App\Models\MasterProfile;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MasterController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('masterProfile')
            ->where('role', Role::Master->value)
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $masters = $query->paginate(20)->withQueryString();

        return view('admin.masters.index', compact('masters'));
    }

    public function create(): View
    {
        $categories = ServiceCategory::all();

        return view('admin.masters.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:3000'],
            'years_experience' => ['required', 'integer', 'min:0'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['exists:service_categories,id'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.required' => "Ім'я обов'язкове.",
            'email.required' => 'Email обов\'язковий.',
            'email.unique' => 'Цей email вже використовується.',
            'city.required' => 'Місто обов\'язкове.',
            'categories.required' => 'Оберіть категорію.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => Role::Master,
            'is_active' => true,
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('masters', 'public');
        }

        $profile = MasterProfile::create([
            'user_id' => $user->id,
            'bio' => $validated['bio'] ?? null,
            'city' => $validated['city'],
            'years_experience' => $validated['years_experience'],
            'photo_path' => $photoPath,
        ]);

        $profile->categories()->sync($validated['categories']);

        return redirect()->route('admin.masters.index')->with('success', 'Майстра створено.');
    }

    public function edit(User $master): View
    {
        abort_unless($master->isMaster(), 404);
        $profile = $master->masterProfile()->with(['categories', 'photos'])->first();
        $categories = ServiceCategory::all();

        return view('admin.masters.edit', compact('master', 'profile', 'categories'));
    }

    public function update(Request $request, User $master): RedirectResponse
    {
        abort_unless($master->isMaster(), 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:3000'],
            'years_experience' => ['required', 'integer', 'min:0'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['exists:service_categories,id'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.required' => "Ім'я обов'язкове.",
            'city.required' => 'Місто обов\'язкове.',
            'categories.required' => 'Оберіть категорію.',
        ]);

        $master->update(['name' => $validated['name'], 'phone' => $validated['phone']]);

        $profile = $master->masterProfile ?? MasterProfile::create(['user_id' => $master->id, 'city' => '']);

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
            'photo_path' => $validated['photo_path'] ?? $profile->photo_path,
        ]);

        $profile->categories()->sync($validated['categories']);

        return redirect()->route('admin.masters.index')->with('success', 'Майстра оновлено.');
    }

    public function deletePhoto(MasterPhoto $photo): RedirectResponse
    {
        Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();

        return back()->with('success', 'Фото видалено.');
    }
}
