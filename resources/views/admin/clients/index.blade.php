@extends('layouts.admin')
@section('title', 'Клієнти')

@section('content')
<h1 class="text-2xl font-bold text-slate-900 mb-6 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Клієнти</h1>

<form method="GET" class="mb-5">
    <div class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Пошук за іменем або email..."
               class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
        <button type="submit" class="rounded-lg bg-slate-700 text-white px-4 py-2 text-sm font-medium hover:bg-slate-900 transition">Шукати</button>
    </div>
</form>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-100 text-slate-600 uppercase text-xs">
                <th class="px-4 py-3 text-left">Клієнт</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-left">Телефон</th>
                <th class="px-4 py-3 text-left">Статус</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($clients as $client)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-medium text-slate-800">{{ $client->name }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $client->email }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $client->phone ?? '—' }}</td>
                <td class="px-4 py-3">
                    @if($client->is_active)
                        <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-medium">Активний</span>
                    @else
                        <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-medium">Деактивований</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right">
                    <form method="POST" action="{{ route('admin.clients.toggleActive', $client) }}">
                        @csrf
                        <button type="submit" class="text-sm {{ $client->is_active ? 'text-red-600 hover:text-red-800' : 'text-emerald-600 hover:text-emerald-800' }} font-medium">
                            {{ $client->is_active ? 'Деактивувати' : 'Активувати' }}
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Клієнтів не знайдено</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $clients->withQueryString()->links() }}</div>
@endsection
