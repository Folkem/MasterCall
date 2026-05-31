@extends('layouts.admin')
@section('title', 'Редагувати майстра')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-7">
        <h1 class="text-2xl font-bold text-slate-900 mb-6 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Редагувати: {{ $master->name }}</h1>
        <form method="POST" action="{{ route('admin.masters.update', $master) }}" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Ім'я</label>
                    <input type="text" name="name" value="{{ old('name', $master->name) }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('name') border-red-500 @enderror">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Телефон</label>
                    <input type="tel" name="phone" value="{{ old('phone', $master->phone) }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Місто</label>
                    <input type="text" name="city" value="{{ old('city', $profile?->city) }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('city') border-red-500 @enderror">
                    @error('city') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Досвід (роки)</label>
                    <input type="number" name="years_experience" value="{{ old('years_experience', $profile?->years_experience ?? 0) }}" min="0"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Про майстра</label>
                <textarea name="bio" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('bio', $profile?->bio) }}</textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Категорії</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($categories as $cat)
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                               {{ (session()->hasOldInput() ? in_array($cat->id, old('categories', [])) : ($profile && $profile->categories->contains($cat))) ? 'checked' : '' }}
                               class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                        {{ $cat->name }}
                    </label>
                    @endforeach
                </div>
                @error('categories') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Фото профілю</label>
                @if($profile?->photo_path)
                    <img src="{{ $profile->photoUrl() }}" alt="Фото" class="w-16 h-16 rounded-full object-cover mb-2">
                @endif
                <input type="file" name="photo" accept="image/*" class="text-sm text-slate-600">
            </div>
            <div class="mb-4">
                <p class="text-sm font-medium text-slate-700 mb-2">Галерея</p>
                @if($profile?->photos->isNotEmpty())
                <div class="grid grid-cols-4 gap-2 mb-3">
                    @foreach($profile->photos as $photo)
                    <div class="relative aspect-square rounded-lg overflow-hidden bg-slate-100 group">
                        <img src="{{ $photo->url() }}" alt="" class="w-full h-full object-cover">
                        <form method="POST" action="{{ route('admin.masters.photos.delete', $photo) }}" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Видалити?')" class="rounded-full bg-red-600 text-white p-1">
                                @include('components.icon', ['name' => 'trash-2', 'class' => 'w-3.5 h-3.5'])
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif
                <label class="block text-xs text-slate-500 mb-1">Додати фото до галереї</label>
                <input type="file" name="galleries[]" accept="image/*" multiple class="text-sm text-slate-600">
                @error('galleries.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-3">
                <button type="submit" class="rounded-lg bg-teal-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-teal-700 transition">Зберегти</button>
                <a href="{{ route('admin.masters.index') }}" class="rounded-lg border border-slate-200 text-slate-600 px-5 py-2.5 text-sm font-medium hover:bg-slate-50 transition">Скасувати</a>
            </div>
        </form>
    </div>
</div>
@endsection
