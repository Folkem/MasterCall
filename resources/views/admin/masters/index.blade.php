@extends('layouts.admin')
@section('title', 'Майстри')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Майстри</h1>
    <a href="{{ route('admin.masters.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-teal-600 text-white px-4 py-2 text-sm font-semibold hover:bg-teal-700 transition">
        @include('components.icon', ['name' => 'plus', 'class' => 'w-4 h-4'])
        Додати майстра
    </a>
</div>

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
                <th class="px-4 py-3 text-left">Майстер</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-left">Місто</th>
                <th class="px-4 py-3 text-left">Статус</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($masters as $master)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-medium text-slate-800">{{ $master->name }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $master->email }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $master->masterProfile?->city ?? '—' }}</td>
                <td class="px-4 py-3">
                    @if($master->is_active)
                        <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-medium">Активний</span>
                    @else
                        <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-medium">Неактивний</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('admin.masters.edit', $master) }}" class="text-teal-600 hover:text-teal-800 text-sm font-medium">Редагувати</a>
                    <form method="POST" action="{{ route('admin.masters.toggleActive', $master) }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm {{ $master->is_active ? 'text-red-600 hover:text-red-800' : 'text-emerald-600 hover:text-emerald-800' }} font-medium ml-3">
                            {{ $master->is_active ? 'Деактивувати' : 'Активувати' }}
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Майстрів не знайдено</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $masters->withQueryString()->links() }}</div>
@endsection
