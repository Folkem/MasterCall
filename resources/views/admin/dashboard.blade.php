@extends('layouts.admin')
@section('title', 'Дашборд')

@section('content')
<h1 class="text-2xl font-bold text-slate-900 mb-6 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Дашборд</h1>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 border-t-4 border-t-teal-500">
        <p class="text-sm text-slate-500 mb-1">Замовлень сьогодні</p>
        <p class="text-3xl font-bold text-slate-900">{{ $ordersToday }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 border-t-4 border-t-yellow-400">
        <p class="text-sm text-slate-500 mb-1">Дохід цього місяця</p>
        <p class="text-3xl font-bold text-slate-900">{{ number_format($revenueThisMonth, 0, '.', ' ') }} <span class="text-sm font-normal text-slate-500">грн</span></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 border-t-4 border-t-blue-500">
        <p class="text-sm text-slate-500 mb-1">Майстрів</p>
        <p class="text-3xl font-bold text-slate-900">{{ $totalMasters }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 border-t-4 border-t-emerald-500">
        <p class="text-sm text-slate-500 mb-1">Клієнтів</p>
        <p class="text-3xl font-bold text-slate-900">{{ $totalClients }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
    <h2 class="font-semibold text-slate-800 mb-4" style="font-family: 'Space Grotesk', sans-serif;">Топ майстрів за виконаними замовленнями</h2>
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-100 text-slate-600 uppercase text-xs">
                <th class="px-4 py-2 text-left rounded-l-lg">#</th>
                <th class="px-4 py-2 text-left">Майстер</th>
                <th class="px-4 py-2 text-right rounded-r-lg">Виконано</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($topMasters as $i => $master)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-400">{{ $i + 1 }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('admin.masters.edit', $master) }}" class="font-medium text-teal-700 hover:underline">{{ $master->name }}</a>
                </td>
                <td class="px-4 py-3 text-right font-semibold text-slate-800">{{ $master->completed_orders_count }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-4 py-6 text-center text-slate-400">Даних ще немає</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
