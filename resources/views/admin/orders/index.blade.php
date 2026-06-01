@extends('layouts.admin')
@section('title', 'Замовлення')

@section('content')
<h1 class="text-2xl font-bold text-slate-900 mb-6 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Замовлення</h1>

<form method="GET" class="mb-5">
    <div class="flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Пошук за клієнтом або майстром..."
               class="flex-1 min-w-48 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
        <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
            <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Всі статуси</option>
            @foreach($statuses as $status)
                <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-slate-700 text-white px-4 py-2 text-sm font-medium hover:bg-slate-900 transition">Фільтрувати</button>
    </div>
</form>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-100 text-slate-600 uppercase text-xs">
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Клієнт</th>
                <th class="px-4 py-3 text-left">Майстер</th>
                <th class="px-4 py-3 text-left">Дата</th>
                <th class="px-4 py-3 text-left">Ціна</th>
                <th class="px-4 py-3 text-left">Статус</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($orders as $order)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-400 font-mono text-xs">{{ $order->id }}</td>
                <td class="px-4 py-3 font-medium text-slate-800">{{ $order->client->name }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $order->master->name }}</td>
                <td class="px-4 py-3 text-slate-500 whitespace-nowrap">{{ $order->scheduled_at->format('d.m.Y H:i') }}</td>
                <td class="px-4 py-3 text-slate-700 font-medium">{{ $order->price ? number_format($order->price, 0, '.', ' ') . ' грн' : '—' }}</td>
                <td class="px-4 py-3"><x-status-badge :status="$order->status" /></td>
                <td class="px-4 py-3">
                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="flex items-center gap-1">
                        @csrf
                        <select name="status" class="rounded border border-slate-300 px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-teal-500">
                            @foreach($statuses as $s)
                                <option value="{{ $s->value }}" {{ $order->status === $s ? 'selected' : '' }}>{{ $s->label() }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="text-teal-600 hover:text-teal-800 text-xs font-medium px-1">OK</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">Замовлень не знайдено</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->withQueryString()->links() }}</div>
@endsection
