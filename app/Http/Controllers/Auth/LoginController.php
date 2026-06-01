<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Електронна пошта обов\'язкова.',
            'email.email' => 'Введіть коректну електронну пошту.',
            'password.required' => 'Пароль обов\'язковий.',
        ]);

        if (! Auth::attempt($validated, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Невірна електронна пошта або пароль.'])->withInput($request->only('email'));
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();

            return back()->withErrors(['email' => 'Ваш обліковий запис деактивовано. Зверніться до адміністратора.'])->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return match ($user->role) {
            Role::Admin => redirect()->route('admin.dashboard'),
            Role::Master => redirect()->route('cabinet.orders.index'),
            default => redirect()->route('account.orders.index'),
        };
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
