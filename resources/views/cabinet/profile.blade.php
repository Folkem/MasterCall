@extends('layouts.cabinet')
@section('title', 'Профіль майстра')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Профіль майстра</h1>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <form method="POST" action="{{ route('cabinet.profile.update') }}" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Фото профілю</label>
                @if($profile->photo_path)
                    <img src="{{ $profile->photoUrl() }}" alt="Фото" class="w-20 h-20 rounded-full object-cover mb-2">
                @endif
                <input type="file" name="photo" accept="image/*" class="block text-sm text-slate-600">
                @error('photo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Місто</label>
                <input type="text" name="city" value="{{ old('city', $profile->city) }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('city') border-red-500 @enderror">
                @error('city') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Досвід (роки)</label>
                <input type="number" name="years_experience" value="{{ old('years_experience', $profile->years_experience) }}" min="0"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('years_experience') border-red-500 @enderror">
                @error('years_experience') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Про себе</label>
                <textarea name="bio" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('bio', $profile->bio) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Категорії</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($categories as $cat)
                    <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                        <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                               {{ $profile->categories->contains($cat) || in_array($cat->id, old('categories', [])) ? 'checked' : '' }}
                               class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                        {{ $cat->name }}
                    </label>
                    @endforeach
                </div>
                @error('categories') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                    <input type="checkbox" name="is_available" value="1"
                           {{ old('is_available', $profile->is_available) ? 'checked' : '' }}
                           class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                    Доступний для замовлень
                </label>
            </div>

            <button type="submit" class="rounded-lg bg-teal-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-teal-700 transition">Зберегти профіль</button>
        </form>
    </div>

    {{-- Gallery --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">Портфоліо</h2>
        @if($profile->photos->isNotEmpty())
        <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-4">
            @foreach($profile->photos as $photo)
            <div class="relative aspect-square rounded-lg overflow-hidden bg-slate-100 group">
                <img src="{{ $photo->url() }}" alt="Галерея" class="w-full h-full object-cover">
                <form method="POST" action="{{ route('cabinet.profile.photos.delete', $photo) }}" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Видалити фото?')" class="rounded-full bg-red-600 text-white p-1.5">
                        @include('components.icon', ['name' => 'trash-2', 'class' => 'w-4 h-4'])
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @endif
        <form method="POST" action="{{ route('cabinet.profile.photos.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="flex items-center gap-3">
                <input type="file" name="photo" accept="image/*" required class="text-sm text-slate-600">
                <button type="submit" class="rounded-lg bg-slate-700 text-white px-4 py-2 text-sm font-medium hover:bg-slate-900 transition">Додати фото</button>
            </div>
            @error('photo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </form>
    </div>
</div>
@endsection
