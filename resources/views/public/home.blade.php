@extends('layouts.public')
@section('title', 'Головна')

@section('content')
{{-- Hero --}}
<section class="bg-gradient-to-br from-teal-700 to-slate-800 rounded-2xl text-white px-8 py-16 mb-12 text-center">
    <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-4" style="font-family: 'Space Grotesk', sans-serif;">
        Виклик майстра <span class="text-yellow-400">швидко</span> і надійно
    </h1>
    <p class="text-slate-300 text-lg mb-8 max-w-2xl mx-auto">Знайдіть перевіреного майстра для будь-якого ремонту та обслуговування.</p>
    <form method="GET" action="{{ route('masters.index') }}" class="max-w-xl mx-auto flex gap-2">
        <input type="text" name="search" placeholder="Пошук майстра або послуги..."
               class="flex-1 rounded-lg px-4 py-3 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
        <button type="submit" class="rounded-lg bg-yellow-400 text-slate-900 px-6 py-3 font-semibold hover:bg-yellow-300 transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Знайти
        </button>
    </form>
</section>

{{-- Categories --}}
<section class="mb-12">
    <h2 class="text-2xl font-bold text-slate-900 mb-6 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Категорії послуг</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($categories as $cat)
        <a href="{{ route('masters.index', ['category' => $cat->slug]) }}"
           class="group bg-white rounded-xl shadow-sm hover:shadow-md transition p-4 text-center border-t-4 border-teal-500 flex flex-col items-center gap-2">
            @include('components.icon', ['name' => $cat->icon, 'class' => 'w-8 h-8 text-teal-600 group-hover:text-teal-700'])
            <span class="text-sm font-medium text-slate-700 group-hover:text-teal-700">{{ $cat->name }}</span>
        </a>
        @endforeach
    </div>
</section>

{{-- Top Masters --}}
<section>
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Топ майстрів</h2>
        <a href="{{ route('masters.index') }}" class="text-sm text-teal-600 hover:text-teal-800 font-medium flex items-center gap-1">
            Всі майстри
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($topMasters as $profile)
        <a href="{{ route('masters.show', $profile->user) }}"
           class="group bg-white rounded-xl shadow-sm hover:shadow-md transition overflow-hidden border border-slate-100">
            <div class="relative h-40 bg-slate-100">
                <img src="{{ $profile->photoUrl() }}" alt="{{ $profile->user->name }}"
                     class="w-full h-full object-cover">
                @if($profile->is_available)
                    <span class="absolute top-2 right-2 bg-yellow-400 text-slate-900 text-xs font-bold px-2 py-0.5 rounded-full">Доступний</span>
                @endif
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-slate-900 group-hover:text-teal-700 transition">{{ $profile->user->name }}</h3>
                <p class="text-xs text-slate-500 mb-2">{{ $profile->city }}</p>
                <div class="flex items-center gap-1">
                    @php $rating = $profile->averageRating(); @endphp
                    @for($i = 1; $i <= 5; $i++)
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 {{ $i <= $rating ? 'text-yellow-400 fill-yellow-400' : 'text-slate-300' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    @endfor
                    <span class="text-xs text-slate-500 ml-1">{{ $rating }} ({{ $profile->reviewCount() }})</span>
                </div>
            </div>
        </a>
        @empty
        <div class="col-span-4 text-center text-slate-400 py-8">Майстрів ще немає</div>
        @endforelse
    </div>
</section>
@endsection
