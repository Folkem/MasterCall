<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => "Ім'я обов'язкове.",
            'name.max' => "Ім'я не може перевищувати 255 символів.",
            'email.required' => 'Електронна пошта обов\'язкова.',
            'email.email' => 'Введіть коректну електронну пошту.',
            'email.unique' => 'Цей email вже зареєстровано.',
            'password.required' => 'Пароль обов\'язковий.',
            'password.min' => 'Пароль має містити щонайменше 8 символів.',
            'password.confirmed' => 'Паролі не збігаються.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'phone' => $validated['phone'] ?? null,
            'role' => Role::Client,
            'is_active' => true,
        ]);

        Auth::login($user);

        return redirect()->route('account.orders.index')->with('success', 'Ласкаво просимо! Реєстрація успішна.');
    }
}
