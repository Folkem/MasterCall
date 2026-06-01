@extends('layouts.account')
@section('title', 'Мій профіль')

@section('content')
<div class="max-w-xl mx-auto space-y-5">
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Мій профіль</h1>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">Особисті дані</h2>
        <form method="POST" action="{{ route('account.profile.update') }}" novalidate>
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Ім'я</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('name') border-red-500 @enderror">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1">Телефон</label>
                <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
            </div>
            <button type="submit" class="rounded-lg bg-teal-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-teal-700 transition">Зберегти</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">Змінити пароль</h2>
        <form method="POST" action="{{ route('account.profile.password') }}" novalidate>
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Поточний пароль</label>
                <input type="password" name="current_password"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('current_password') border-red-500 @enderror">
                @error('current_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Новий пароль</label>
                <input type="password" name="password"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('password') border-red-500 @enderror">
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1">Підтвердження пароля</label>
                <input type="password" name="password_confirmation"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
            </div>
            <button type="submit" class="rounded-lg bg-slate-700 text-white px-5 py-2.5 text-sm font-semibold hover:bg-slate-900 transition">Змінити пароль</button>
        </form>
    </div>
</div>
@endsection
