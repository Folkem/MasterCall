@extends('layouts.account')
@section('title', 'Нове замовлення')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('masters.show', $master) }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-teal-700 mb-6 transition">
        @include('components.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4'])
        Назад до профілю
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-7">
        <h1 class="text-2xl font-bold text-slate-900 mb-6 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">
            Нове замовлення — {{ $master->name }}
        </h1>

        @if($service)
            <div class="bg-teal-50 border border-teal-200 rounded-lg p-3 mb-5">
                <p class="text-sm text-teal-700 font-medium">Обрана послуга: {{ $service->name }}</p>
                <p class="text-sm text-teal-600">{{ $service->priceDisplay() }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('account.orders.store') }}" novalidate>
            @csrf
            <input type="hidden" name="master_id" value="{{ $master->id }}">
            <input type="hidden" name="service_id" value="{{ $service?->id }}">

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Категорія</label>
                <select name="category_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('category_id') border-red-500 @enderror">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (session()->hasOldInput() ? old('category_id') == $cat->id : ($service && $service->category_id === $cat->id)) ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Адреса</label>
                <input type="text" name="address" value="{{ old('address') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('address') border-red-500 @enderror"
                       placeholder="вул. Хрещатик, 1, кв. 5">
                @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Дата та час</label>
                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}"
                       min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('scheduled_at') border-red-500 @enderror">
                @error('scheduled_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Опис роботи</label>
                <textarea name="description" rows="4"
                          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('description') border-red-500 @enderror"
                          placeholder="Опишіть що потрібно зробити...">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full rounded-lg bg-teal-600 text-white py-3 font-semibold hover:bg-teal-700 transition">
                Відправити замовлення
            </button>
        </form>
    </div>
</div>
@endsection
