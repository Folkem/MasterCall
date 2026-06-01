@extends('layouts.account')
@section('title', 'Мої замовлення')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Мої замовлення</h1>
    <a href="{{ route('masters.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-teal-600 text-white px-4 py-2 text-sm font-semibold hover:bg-teal-700 transition">
        @include('components.icon', ['name' => 'plus', 'class' => 'w-4 h-4'])
        Нове замовлення
    </a>
</div>

{{-- Status filter --}}
<div class="flex gap-2 flex-wrap mb-6 overflow-x-auto">
    <a href="{{ route('account.orders.index') }}" class="px-4 py-1.5 rounded-full text-sm font-medium {{ !request('status') ? 'bg-teal-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Всі</a>
    @foreach($statuses as $status)
        <a href="{{ route('account.orders.index', ['status' => $status->value]) }}"
           class="px-4 py-1.5 rounded-full text-sm font-medium {{ request('status') === $status->value ? 'bg-teal-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            {{ $status->label() }}
        </a>
    @endforeach
</div>

@forelse($orders as $order)
<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 mb-4 hover:shadow-md transition">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 mb-2">
                <span class="font-mono text-xs text-slate-400">#{{ $order->id }}</span>
                <x-status-badge :status="$order->status" />
            </div>
            <h3 class="font-semibold text-slate-900">{{ $order->master->name }}</h3>
            <p class="text-sm text-slate-500">{{ $order->category->name }}{{ $order->service ? ' · ' . $order->service->name : '' }}</p>
            <div class="flex flex-wrap gap-4 mt-2 text-xs text-slate-500">
                <span class="flex items-center gap-1">
                    @include('components.icon', ['name' => 'calendar', 'class' => 'w-3.5 h-3.5'])
                    {{ $order->scheduled_at->format('d.m.Y H:i') }}
                </span>
                <span class="flex items-center gap-1">
                    @include('components.icon', ['name' => 'map-pin', 'class' => 'w-3.5 h-3.5'])
                    {{ $order->address }}
                </span>
            </div>
        </div>
        <div class="text-right shrink-0">
            @if($order->price)
                <p class="font-bold text-teal-700">{{ number_format($order->price, 0, '.', ' ') }} грн</p>
            @endif
            <a href="{{ route('account.orders.show', $order) }}" class="mt-2 inline-block rounded-lg border border-slate-200 text-slate-600 px-4 py-1.5 text-sm hover:bg-slate-50 transition">Деталі</a>
        </div>
    </div>
</div>
@empty
<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-12 text-center">
    @include('components.icon', ['name' => 'package', 'class' => 'w-12 h-12 text-slate-300 mx-auto mb-4'])
    <p class="text-slate-500 text-lg font-medium">Замовлень ще немає</p>
    <p class="text-slate-400 text-sm mt-1 mb-4">Знайдіть майстра та зробіть перше замовлення</p>
    <a href="{{ route('masters.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-teal-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-teal-700 transition">
        Знайти майстра
    </a>
</div>
@endforelse

{{ $orders->links() }}
@endsection
