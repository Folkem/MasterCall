@extends('layouts.cabinet')
@section('title', 'Мої послуги')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-slate-900 mb-6 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Мої послуги</h1>

    {{-- Add form --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 mb-6">
        <h2 class="font-semibold text-slate-800 mb-4">Додати послугу</h2>
        <form method="POST" action="{{ route('cabinet.services.store') }}" novalidate>
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Категорія</label>
                    <select name="category_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('category_id') border-red-500 @enderror">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Назва</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('name') border-red-500 @enderror">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Ціна (грн)</label>
                    <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('price') border-red-500 @enderror">
                    @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Тип ціни</label>
                    <select name="price_type" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="fixed" {{ old('price_type') === 'fixed' ? 'selected' : '' }}>Фіксована</option>
                        <option value="from" {{ old('price_type') === 'from' ? 'selected' : '' }}>Від</option>
                        <option value="hourly" {{ old('price_type') === 'hourly' ? 'selected' : '' }}>За годину</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Тривалість (хв) <span class="text-slate-400">(необов'язково)</span></label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" min="1"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Опис <span class="text-slate-400">(необов'язково)</span></label>
                    <textarea name="description" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('description') }}</textarea>
                </div>
            </div>
            <button type="submit" class="mt-4 rounded-lg bg-teal-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-teal-700 transition">Додати послугу</button>
        </form>
    </div>

    {{-- Services list --}}
    @forelse($services as $service)
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 mb-3 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="font-semibold text-slate-800">{{ $service->name }}</span>
                <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">{{ $service->category->name }}</span>
            </div>
            <p class="text-sm text-teal-700 font-medium">{{ $service->priceDisplay() }}</p>
            @if($service->duration_minutes)
                <p class="text-xs text-slate-400">{{ $service->duration_minutes }} хв</p>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('cabinet.services.edit', $service) }}" class="rounded-lg border border-slate-200 text-slate-600 px-3 py-1.5 text-sm hover:bg-slate-50 transition">Редагувати</a>
            <form method="POST" action="{{ route('cabinet.services.destroy', $service) }}" onsubmit="return confirm('Видалити послугу?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg border border-red-200 text-red-600 px-3 py-1.5 text-sm hover:bg-red-50 transition">Видалити</button>
            </form>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-10 text-center">
        <p class="text-slate-400">Послуг ще немає. Додайте першу!</p>
    </div>
    @endforelse
</div>
@endsection
