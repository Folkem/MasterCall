@extends('layouts.public')
@section('title', 'Майстри')

@section('content')
<div class="flex flex-col md:flex-row gap-6">
    {{-- Filters --}}
    <aside class="w-full md:w-64 shrink-0">
        <form method="GET" action="{{ route('masters.index') }}" class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 sticky top-24">
            <h2 class="font-semibold text-slate-800 mb-4 text-sm uppercase tracking-wide">Фільтри</h2>

            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-600 mb-1">Пошук</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
                       placeholder="Ім'я або послуга">
            </div>

            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-600 mb-1">Категорія</label>
                <select name="category" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="">Всі категорії</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-600 mb-1">Місто</label>
                <select name="city" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="">Всі міста</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-5">
                <label class="block text-xs font-medium text-slate-600 mb-1">Сортування</label>
                <select name="sort" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="rating" {{ $sort === 'rating' ? 'selected' : '' }}>За рейтингом</option>
                    <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Ціна: від низької</option>
                    <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Ціна: від високої</option>
                </select>
            </div>

            <button type="submit" class="w-full rounded-lg bg-teal-600 text-white py-2.5 text-sm font-semibold hover:bg-teal-700 transition">
                Застосувати
            </button>
            @if(request()->hasAny(['search', 'category', 'city', 'sort']))
                <a href="{{ route('masters.index') }}" class="block text-center mt-2 text-xs text-slate-500 hover:text-red-600">Скинути фільтри</a>
            @endif
        </form>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Майстри</h1>
            <span class="text-sm text-slate-500">Знайдено: {{ $total }}</span>
        </div>

        @if($paginatedMasters->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
                @include('components.icon', ['name' => 'search', 'class' => 'w-12 h-12 text-slate-300 mx-auto mb-4'])
                <p class="text-slate-500 text-lg font-medium">Майстрів не знайдено</p>
                <p class="text-slate-400 text-sm mt-1">Спробуйте змінити критерії пошуку</p>
            </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($paginatedMasters as $profile)
            <a href="{{ route('masters.show', $profile->user) }}"
               class="group bg-white rounded-xl shadow-sm hover:shadow-md transition overflow-hidden border border-slate-100">
                <div class="h-48 bg-slate-100 overflow-hidden relative">
                    <img src="{{ $profile->photoUrl() }}" alt="{{ $profile->user->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @if($profile->is_available)
                        <span class="absolute top-3 right-3 bg-yellow-400 text-slate-900 text-xs font-bold px-2 py-0.5 rounded-full">Доступний</span>
                    @else
                        <span class="absolute top-3 right-3 bg-slate-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">Зайнятий</span>
                    @endif
                </div>
                <div class="p-4">
                    <div class="h-1 w-8 rounded-full bg-teal-500 mb-3"></div>
                    <h3 class="font-bold text-slate-900 text-base group-hover:text-teal-700 transition">{{ $profile->user->name }}</h3>
                    <div class="flex items-center gap-1 text-xs text-slate-500 mt-1">
                        @include('components.icon', ['name' => 'map-pin', 'class' => 'w-3.5 h-3.5'])
                        {{ $profile->city }}
                    </div>
                    <div class="flex flex-wrap gap-1 mt-2 mb-3">
                        @foreach($profile->categories->take(2) as $cat)
                            <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">{{ $cat->name }}</span>
                        @endforeach
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-0.5">
                            @php $rating = $profile->averageRating(); @endphp
                            @for($i = 1; $i <= 5; $i++)
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 {{ $i <= $rating ? 'text-yellow-400 fill-yellow-400' : 'text-slate-300' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            @endfor
                            <span class="text-xs text-slate-500 ml-1">{{ $rating }} ({{ $profile->reviewCount() }})</span>
                        </div>
                        @if($profile->minServicePrice())
                            <span class="text-xs font-medium text-teal-700">від {{ number_format($profile->minServicePrice(), 0, '.', ' ') }} грн</span>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($total > $perPage)
        <div class="flex items-center justify-center gap-2 mt-8">
            @for($p = 1; $p <= ceil($total / $perPage); $p++)
                <a href="{{ request()->fullUrlWithQuery(['page' => $p]) }}"
                   class="w-9 h-9 flex items-center justify-center rounded-lg text-sm {{ $p == $page ? 'bg-teal-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                    {{ $p }}
                </a>
            @endfor
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
