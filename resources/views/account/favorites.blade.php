@extends('layouts.account')
@section('title', 'Обрані майстри')

@section('content')
<h1 class="text-2xl font-bold text-slate-900 mb-6 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Обрані майстри</h1>

@forelse($favorites as $fav)
@php $profile = $fav->master->masterProfile; @endphp
<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 mb-4 flex items-center gap-4">
    @if($profile)
    <img src="{{ $profile->photoUrl() }}" alt="{{ $fav->master->name }}" class="w-14 h-14 rounded-full object-cover bg-slate-100 shrink-0">
    @else
    <div class="w-14 h-14 rounded-full bg-slate-200 shrink-0 flex items-center justify-center">
        @include('components.icon', ['name' => 'user', 'class' => 'w-7 h-7 text-slate-400'])
    </div>
    @endif
    <div class="flex-1 min-w-0">
        <h3 class="font-semibold text-slate-900">{{ $fav->master->name }}</h3>
        @if($profile)
            <p class="text-sm text-slate-500">{{ $profile->city }}</p>
            <div class="flex flex-wrap gap-1 mt-1">
                @foreach($profile->categories->take(2) as $cat)
                    <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">{{ $cat->name }}</span>
                @endforeach
            </div>
        @endif
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <a href="{{ route('masters.show', $fav->master) }}" class="rounded-lg border border-slate-200 text-slate-600 px-3 py-1.5 text-sm hover:bg-slate-50 transition">Переглянути</a>
        <form method="POST" action="{{ route('account.favorites.toggle', $fav->master) }}">
            @csrf
            <button type="submit" class="rounded-lg border border-red-200 text-red-600 px-3 py-1.5 text-sm hover:bg-red-50 transition">Видалити</button>
        </form>
    </div>
</div>
@empty
<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-12 text-center">
    @include('components.icon', ['name' => 'heart', 'class' => 'w-12 h-12 text-slate-300 mx-auto mb-4'])
    <p class="text-slate-500 text-lg font-medium">Обраних майстрів немає</p>
    <p class="text-slate-400 text-sm mt-1 mb-4">Додайте майстрів до обраних на їх сторінці</p>
    <a href="{{ route('masters.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-teal-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-teal-700 transition">
        Знайти майстра
    </a>
</div>
@endforelse
@endsection
