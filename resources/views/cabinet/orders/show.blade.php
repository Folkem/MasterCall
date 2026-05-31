@extends('layouts.cabinet')
@section('title', 'Замовлення #' . $order->id)

@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('cabinet.orders.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-teal-400 mb-6 transition">
        @include('components.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4'])
        До замовлень
    </a>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="md:col-span-2 space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-xl font-bold text-slate-900" style="font-family: 'Space Grotesk', sans-serif;">Замовлення #{{ $order->id }}</h1>
                    <x-status-badge :status="$order->status" />
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Клієнт</span>
                        <span class="font-medium text-slate-800">{{ $order->client->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Телефон</span>
                        <span class="font-medium text-slate-800">{{ $order->client->phone ?? 'Не вказано' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Категорія</span>
                        <span class="font-medium text-slate-800">{{ $order->category->name }}</span>
                    </div>
                    @if($order->service)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Послуга</span>
                        <span class="font-medium text-slate-800">{{ $order->service->name }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-slate-500">Дата</span>
                        <span class="font-medium text-slate-800">{{ $order->scheduled_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Адреса</span>
                        <span class="font-medium text-slate-800 text-right max-w-xs">{{ $order->address }}</span>
                    </div>
                    @if($order->price)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Ціна</span>
                        <span class="font-bold text-teal-700">{{ number_format($order->price, 0, '.', ' ') }} грн</span>
                    </div>
                    @endif
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100">
                    <p class="text-xs text-slate-500 mb-1">Опис</p>
                    <p class="text-sm text-slate-700">{{ $order->description }}</p>
                </div>
            </div>

            {{-- Actions by status --}}
            @if($order->status->value === 'pending')
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 space-y-4">
                <h3 class="font-semibold text-slate-800">Прийняти замовлення</h3>
                <form method="POST" action="{{ route('cabinet.orders.accept', $order) }}" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Ціна (грн)</label>
                        <input type="number" name="price" value="{{ old('price', $order->price ?? '') }}" step="0.01" min="1"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('price') border-red-500 @enderror">
                        @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Примітка <span class="text-slate-400">(необов'язково)</span></label>
                        <textarea name="master_note" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('master_note') }}</textarea>
                    </div>
                    <button type="submit" class="rounded-lg bg-teal-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-teal-700 transition">Прийняти</button>
                </form>
                <div class="border-t border-slate-100 pt-4">
                    <form method="POST" action="{{ route('cabinet.orders.decline', $order) }}" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Причина відмови</label>
                            <textarea name="master_note" rows="2" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('master_note') border-red-500 @enderror">{{ old('master_note') }}</textarea>
                            @error('master_note') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="rounded-lg bg-red-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-red-700 transition">Відхилити</button>
                    </form>
                </div>
            </div>
            @endif

            @if(in_array($order->status->value, ['accepted', 'confirmed']))
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 space-y-3">
                @if($order->status->value === 'confirmed')
                <form method="POST" action="{{ route('cabinet.orders.start', $order) }}">
                    @csrf
                    <button type="submit" class="rounded-lg bg-amber-500 text-white px-5 py-2.5 text-sm font-semibold hover:bg-amber-600 transition w-full">
                        Розпочати виконання
                    </button>
                </form>
                @endif
                <form method="POST" action="{{ route('cabinet.orders.decline', $order) }}" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Причина відмови</label>
                        <textarea name="master_note" rows="2" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
                    </div>
                    <button type="submit" class="rounded-lg border border-red-300 text-red-600 px-5 py-2.5 text-sm font-medium hover:bg-red-50 transition">Відхилити</button>
                </form>
            </div>
            @endif

            @if($order->status->value === 'in_progress')
            <form method="POST" action="{{ route('cabinet.orders.complete', $order) }}">
                @csrf
                <button type="submit" class="rounded-lg bg-emerald-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-emerald-700 transition">
                    Завершити роботу
                </button>
            </form>
            @endif

            @if($order->status->value === 'completed')
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                <h3 class="font-semibold text-slate-800 mb-3">Звіт про роботу</h3>
                <form method="POST" action="{{ route('cabinet.orders.report.store', $order) }}" novalidate>
                    @csrf
                    <textarea name="content" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('content') border-red-500 @enderror"
                              placeholder="Опишіть виконану роботу...">{{ old('content', $order->workReport?->content) }}</textarea>
                    @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    <button type="submit" class="mt-3 rounded-lg bg-teal-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-teal-700 transition">
                        {{ $order->workReport ? 'Оновити звіт' : 'Зберегти звіт' }}
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- Chat --}}
        <div class="md:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 flex flex-col" style="height: 500px;" id="chat">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="font-semibold text-sm text-slate-800">Чат з клієнтом</h3>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-3">
                    @forelse($order->messages as $msg)
                    <div class="{{ $msg->sender_id === auth()->id() ? 'flex justify-end' : 'flex justify-start' }}">
                        <div class="max-w-xs rounded-lg px-3 py-2 text-sm {{ $msg->sender_id === auth()->id() ? 'bg-teal-600 text-white' : 'bg-slate-100 text-slate-700' }}">
                            <p>{{ $msg->body }}</p>
                            <p class="text-xs mt-1 opacity-60">{{ $msg->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-slate-400 text-xs py-4">Повідомлень ще немає</div>
                    @endforelse
                </div>
                <div class="p-3 border-t border-slate-100">
                    <form method="POST" action="{{ route('messages.store', $order) }}" class="flex gap-2">
                        @csrf
                        <input type="text" name="body" placeholder="Написати..." required
                               class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <button type="submit" class="rounded-lg bg-teal-600 text-white p-2 hover:bg-teal-700 transition">
                            @include('components.icon', ['name' => 'send', 'class' => 'w-4 h-4'])
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
