@extends('layouts.account')
@section('title', 'Залишити відгук')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-7">
        <h1 class="text-2xl font-bold text-slate-900 mb-2 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">
            {{ $existing ? 'Редагувати відгук' : 'Залишити відгук' }}
        </h1>
        <p class="text-sm text-slate-500 mb-6">Майстер: {{ $master->name }}</p>

        <form method="POST" action="{{ route('account.reviews.store') }}" novalidate>
            @csrf
            <input type="hidden" name="master_id" value="{{ $master->id }}">

            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-2">Оцінка</label>
                <div x-data="{ rating: {{ $existing?->rating ?? 0 }} }" class="flex gap-1">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" @click="rating = {{ $i }}"
                            class="focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" :class="rating >= {{ $i }} ? 'text-yellow-400 fill-yellow-400' : 'text-slate-300'" class="w-8 h-8 transition-colors hover:text-yellow-400" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </button>
                    @endfor
                    <input type="hidden" name="rating" :value="rating">
                </div>
                @error('rating') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Коментар <span class="text-slate-400">(необов'язково)</span></label>
                <textarea name="comment" rows="4"
                          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
                          placeholder="Розкажіть про ваш досвід...">{{ old('comment', $existing?->comment) }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="rounded-lg bg-teal-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-teal-700 transition">
                    {{ $existing ? 'Оновити відгук' : 'Надіслати відгук' }}
                </button>
                <a href="{{ route('masters.show', $master) }}" class="rounded-lg border border-slate-200 text-slate-600 px-5 py-2.5 text-sm font-medium hover:bg-slate-50 transition">Скасувати</a>
            </div>
        </form>
    </div>
</div>
@endsection
