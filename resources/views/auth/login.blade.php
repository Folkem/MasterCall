@extends('layouts.public')
@section('title', 'Увійти')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
        <h1 class="text-2xl font-bold text-slate-900 mb-6 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Увійти до MasterCall</h1>

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Електронна пошта</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('email') border-red-500 @enderror"
                       required autocomplete="email">
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Пароль</label>
                <input type="password" id="password" name="password"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('password') border-red-500 @enderror"
                       required autocomplete="current-password">
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full rounded-lg bg-teal-600 text-white py-2.5 text-sm font-semibold hover:bg-teal-700 transition">
                Увійти
            </button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-4">
            Немає акаунта? <a href="{{ route('register') }}" class="text-teal-600 hover:text-teal-800 font-medium">Зареєструватись</a>
        </p>
    </div>
</div>
@endsection
