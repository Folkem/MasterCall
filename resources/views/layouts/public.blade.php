<!DOCTYPE html>
<html lang="uk" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MasterCall') — Виклик майстра</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-900" style="font-family: 'Inter', sans-serif;">

{{-- Flash Toast --}}
@if(session('success') || session('error') || session('info'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 -translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-full max-w-md px-4">
    <div class="flex items-center gap-3 rounded-xl shadow-lg overflow-hidden bg-white border border-slate-200">
        <div class="w-1 self-stretch {{ session('error') ? 'bg-red-500' : (session('info') ? 'bg-blue-500' : 'bg-emerald-500') }}"></div>
        <div class="flex items-center gap-2 px-3 py-3 flex-1">
            @if(session('error'))
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            @elseif(session('info'))
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            @endif
            <span class="text-sm text-slate-700">{{ session('success') ?? session('error') ?? session('info') }}</span>
        </div>
        <button @click="show = false" class="pr-3 text-slate-400 hover:text-slate-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
</div>
@endif

{{-- Header --}}
<header class="sticky top-0 z-40 bg-white border-b border-slate-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ mobileOpen: false }">
        <div class="flex items-center justify-between h-16">
            {{-- Brand --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-xl text-teal-700" style="font-family: 'Space Grotesk', sans-serif;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                MasterCall
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
                <a href="{{ route('masters.index') }}" class="text-slate-600 hover:text-teal-700 transition">Майстри</a>
                <a href="{{ route('about') }}" class="text-slate-600 hover:text-teal-700 transition">Про нас</a>
                <a href="{{ route('faq') }}" class="text-slate-600 hover:text-teal-700 transition">FAQ</a>
                <a href="{{ route('contacts') }}" class="text-slate-600 hover:text-teal-700 transition">Контакти</a>
            </nav>

            {{-- Auth links --}}
            <div class="hidden md:flex items-center gap-3">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-sm text-slate-600 hover:text-teal-700">Адмін</a>
                    @elseif(auth()->user()->isMaster())
                        <a href="{{ route('cabinet.orders.index') }}" class="text-sm text-slate-600 hover:text-teal-700">Кабінет</a>
                    @else
                        <a href="{{ route('account.orders.index') }}" class="text-sm text-slate-600 hover:text-teal-700">Мої замовлення</a>
                        @include('components.notification-bell')
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-slate-500 hover:text-red-600 transition">Вийти</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-slate-600 hover:text-teal-700">Увійти</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-1 rounded-lg bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-700 transition">Реєстрація</a>
                @endauth
            </div>

            {{-- Mobile toggle --}}
            <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                <svg x-show="mobileOpen" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileOpen" x-transition class="md:hidden pb-4 border-t border-slate-100 mt-1">
            <nav class="flex flex-col gap-3 pt-3 text-sm font-medium">
                <a href="{{ route('masters.index') }}" class="text-slate-600 hover:text-teal-700">Майстри</a>
                <a href="{{ route('about') }}" class="text-slate-600 hover:text-teal-700">Про нас</a>
                <a href="{{ route('faq') }}" class="text-slate-600 hover:text-teal-700">FAQ</a>
                <a href="{{ route('contacts') }}" class="text-slate-600 hover:text-teal-700">Контакти</a>
                @auth
                    @if(auth()->user()->isMaster())
                        <a href="{{ route('cabinet.orders.index') }}" class="text-slate-600 hover:text-teal-700">Кабінет</a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-slate-600 hover:text-teal-700">Адмін</a>
                    @else
                        <a href="{{ route('account.orders.index') }}" class="text-slate-600 hover:text-teal-700">Мої замовлення</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-left text-red-600">Вийти</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-slate-600 hover:text-teal-700">Увійти</a>
                    <a href="{{ route('register') }}" class="text-teal-700 font-semibold">Реєстрація</a>
                @endauth
            </nav>
        </div>
    </div>
</header>

{{-- Main --}}
<main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
    @yield('content')
</main>

{{-- Footer --}}
<footer class="bg-slate-900 text-slate-400 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col md:flex-row items-start justify-between gap-6">
            <div>
                <a href="{{ route('home') }}" class="text-white font-bold text-lg" style="font-family: 'Space Grotesk', sans-serif;">MasterCall</a>
                <p class="text-sm mt-1">Виклик майстра — швидко та надійно.</p>
            </div>
            <nav class="flex flex-wrap gap-6 text-sm">
                <a href="{{ route('masters.index') }}" class="hover:text-white transition">Майстри</a>
                <a href="{{ route('about') }}" class="hover:text-white transition">Про нас</a>
                <a href="{{ route('faq') }}" class="hover:text-white transition">FAQ</a>
                <a href="{{ route('contacts') }}" class="hover:text-white transition">Контакти</a>
            </nav>
        </div>
        <p class="mt-8 text-xs text-center text-slate-600">&copy; {{ date('Y') }} MasterCall. Всі права захищені.</p>
    </div>
</footer>

</body>
</html>
