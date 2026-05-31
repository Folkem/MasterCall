@extends('layouts.public')
@section('title', 'Контакти')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold text-slate-900 mb-6 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Контакти</h1>
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-8 space-y-5">
        <div class="flex items-center gap-4">
            @include('components.icon', ['name' => 'phone', 'class' => 'w-6 h-6 text-teal-600 shrink-0'])
            <div>
                <p class="font-semibold text-slate-800">Телефон</p>
                <p class="text-slate-600">+380 (50) 123-45-67</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            @include('components.icon', ['name' => 'map-pin', 'class' => 'w-6 h-6 text-teal-600 shrink-0'])
            <div>
                <p class="font-semibold text-slate-800">Адреса</p>
                <p class="text-slate-600">вул. Хрещатик, 1, Київ</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            @include('components.icon', ['name' => 'clock', 'class' => 'w-6 h-6 text-teal-600 shrink-0'])
            <div>
                <p class="font-semibold text-slate-800">Режим роботи</p>
                <p class="text-slate-600">Пн–Пт: 9:00 – 18:00</p>
            </div>
        </div>
        <div class="pt-4 border-t border-slate-100">
            <p class="text-slate-600 text-sm">Для технічної підтримки або питань щодо послуг напишіть нам. Ми відповідаємо протягом 24 годин.</p>
        </div>
    </div>
</div>
@endsection
