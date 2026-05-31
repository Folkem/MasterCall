@extends('layouts.public')
@section('title', $master->name)

@section('content')
<div class="max-w-5xl mx-auto">
    {{-- Back --}}
    <a href="{{ route('masters.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-teal-700 mb-6 transition">
        @include('components.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4'])
        Назад до майстрів
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left column --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="h-64 bg-slate-100">
                    <img src="{{ $profile->photoUrl() }}" alt="{{ $master->name }}" class="w-full h-full object-cover">
                </div>
                <div class="p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <h1 class="text-xl font-bold text-slate-900" style="font-family: 'Space Grotesk', sans-serif;">{{ $master->name }}</h1>
                            <div class="flex items-center gap-1 text-sm text-slate-500 mt-1">
                                @include('components.icon', ['name' => 'map-pin', 'class' => 'w-4 h-4'])
                                {{ $profile->city }}
                            </div>
                        </div>
                        @if($profile->is_available)
                            <span class="bg-yellow-400 text-slate-900 text-xs font-bold px-2 py-0.5 rounded-full shrink-0">Доступний</span>
                        @else
                            <span class="bg-slate-200 text-slate-600 text-xs font-bold px-2 py-0.5 rounded-full shrink-0">Зайнятий</span>
                        @endif
                    </div>

                    <div class="mt-3 flex items-center gap-1">
                        @php $avgRating = $profile->averageRating(); @endphp
                        @for($i = 1; $i <= 5; $i++)
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 {{ $i <= $avgRating ? 'text-yellow-400 fill-yellow-400' : 'text-slate-300' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        @endfor
                        <span class="text-sm text-slate-600 ml-1">{{ $avgRating }} ({{ $profile->reviewCount() }} відгуків)</span>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-1">
                        @foreach($profile->categories as $cat)
                            <span class="text-xs bg-teal-50 text-teal-700 px-2 py-0.5 rounded-full border border-teal-200">{{ $cat->name }}</span>
                        @endforeach
                    </div>

                    @if($profile->years_experience > 0)
                        <p class="text-sm text-slate-600 mt-3 flex items-center gap-1">
                            @include('components.icon', ['name' => 'clock', 'class' => 'w-4 h-4'])
                            {{ $profile->years_experience }} {{ $profile->years_experience === 1 ? 'рік' : ($profile->years_experience < 5 ? 'роки' : 'років') }} досвіду
                        </p>
                    @endif

                    @auth
                        @if(auth()->user()->isClient())
                        <form method="POST" action="{{ route('account.favorites.toggle', $master) }}" class="mt-4">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-lg border {{ $isFavorite ? 'border-red-300 text-red-600 bg-red-50 hover:bg-red-100' : 'border-slate-300 text-slate-600 hover:bg-slate-50' }} py-2 text-sm font-medium transition">
                                @include('components.icon', ['name' => 'heart', 'class' => 'w-4 h-4'])
                                {{ $isFavorite ? 'Видалити з обраних' : 'Додати до обраних' }}
                            </button>
                        </form>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        {{-- Right column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Bio --}}
            @if($profile->bio)
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                <h2 class="font-semibold text-slate-800 mb-2" style="font-family: 'Space Grotesk', sans-serif;">Про майстра</h2>
                <p class="text-slate-600 text-sm leading-relaxed">{{ $profile->bio }}</p>
            </div>
            @endif

            {{-- Services --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                <h2 class="font-semibold text-slate-800 mb-4" style="font-family: 'Space Grotesk', sans-serif;">Послуги</h2>
                @forelse($profile->services as $service)
                <div class="flex items-start justify-between py-3 border-b border-slate-100 last:border-0">
                    <div>
                        <p class="font-medium text-slate-800 text-sm">{{ $service->name }}</p>
                        @if($service->description)
                            <p class="text-xs text-slate-500 mt-0.5">{{ $service->description }}</p>
                        @endif
                        @if($service->duration_minutes)
                            <p class="text-xs text-slate-400 mt-0.5 flex items-center gap-1">
                                @include('components.icon', ['name' => 'clock', 'class' => 'w-3.5 h-3.5'])
                                {{ $service->duration_minutes }} хв
                            </p>
                        @endif
                    </div>
                    <div class="text-right shrink-0 ml-4">
                        <p class="font-bold text-teal-700 text-sm">{{ $service->priceDisplay() }}</p>
                        @auth
                            @if(auth()->user()->isClient())
                            <a href="{{ route('account.orders.create', ['master' => $master->id, 'service' => $service->id]) }}"
                               class="mt-1 inline-block text-xs rounded-lg bg-teal-600 text-white px-3 py-1 hover:bg-teal-700 transition">
                                Замовити
                            </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="mt-1 inline-block text-xs rounded-lg bg-teal-600 text-white px-3 py-1 hover:bg-teal-700 transition">Замовити</a>
                        @endauth
                    </div>
                </div>
                @empty
                <p class="text-slate-400 text-sm">Послуги ще не додані</p>
                @endforelse

                @auth
                    @if(auth()->user()->isClient())
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <a href="{{ route('account.orders.create', ['master' => $master->id]) }}"
                           class="inline-flex items-center gap-2 rounded-lg bg-yellow-400 text-slate-900 px-5 py-2.5 font-semibold text-sm hover:bg-yellow-300 transition">
                            @include('components.icon', ['name' => 'calendar', 'class' => 'w-4 h-4'])
                            Зробити загальне замовлення
                        </a>
                    </div>
                    @endif
                @else
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-lg bg-yellow-400 text-slate-900 px-5 py-2.5 font-semibold text-sm hover:bg-yellow-300 transition">
                            @include('components.icon', ['name' => 'calendar', 'class' => 'w-4 h-4'])
                            Замовити послугу
                        </a>
                    </div>
                @endauth
            </div>

            {{-- Gallery --}}
            @if($profile->photos->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                <h2 class="font-semibold text-slate-800 mb-4" style="font-family: 'Space Grotesk', sans-serif;">Портфоліо</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($profile->photos as $photo)
                    <div class="aspect-square rounded-lg overflow-hidden bg-slate-100">
                        <img src="{{ $photo->url() }}" alt="Робота майстра" class="w-full h-full object-cover hover:scale-105 transition duration-300">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Reviews --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-slate-800" style="font-family: 'Space Grotesk', sans-serif;">Відгуки ({{ $reviews->count() }})</h2>
                    @auth
                        @if(auth()->user()->isClient())
                        <a href="{{ route('account.reviews.create', ['master' => $master->id]) }}"
                           class="text-sm text-teal-600 hover:text-teal-800 font-medium">
                            + Залишити відгук
                        </a>
                        @endif
                    @endauth
                </div>

                @forelse($reviews as $review)
                <div class="py-4 border-b border-slate-100 last:border-0">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-medium text-slate-800 text-sm">{{ $review->client->name }}</p>
                            <p class="text-xs text-slate-400">{{ $review->created_at->format('d.m.Y') }}</p>
                        </div>
                        <div class="flex items-center gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400 fill-yellow-400' : 'text-slate-300' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            @endfor
                        </div>
                    </div>
                    @if($review->comment)
                        <p class="text-sm text-slate-600 mt-2">{{ $review->comment }}</p>
                    @endif
                </div>
                @empty
                <div class="text-center py-6 text-slate-400 text-sm">Відгуків ще немає</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
