@extends('layouts.public')
@section('title', 'Реєстрація')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
        <h1 class="text-2xl font-bold text-slate-900 mb-6 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Реєстрація</h1>

        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Ім'я</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('name') border-red-500 @enderror"
                       required autocomplete="name">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Електронна пошта</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('email') border-red-500 @enderror"
                       required autocomplete="email">
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Телефон <span class="text-slate-400">(необов'язково)</span></label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('phone') border-red-500 @enderror"
                       autocomplete="tel">
                @error('phone')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Пароль</label>
                <input type="password" id="password" name="password"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('password') border-red-500 @enderror"
                       required autocomplete="new-password">
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Підтвердження пароля</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
                       required autocomplete="new-password">
            </div>

            <button type="submit" class="w-full rounded-lg bg-teal-600 text-white py-2.5 text-sm font-semibold hover:bg-teal-700 transition">
                Зареєструватись
            </button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-4">
            Вже маєте акаунт? <a href="{{ route('login') }}" class="text-teal-600 hover:text-teal-800 font-medium">Увійти</a>
        </p>
    </div>
</div>
@endsection
