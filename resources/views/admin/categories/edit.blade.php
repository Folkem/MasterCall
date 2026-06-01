@extends('layouts.admin')
@section('title', 'Редагувати категорію')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-7">
        <h1 class="text-2xl font-bold text-slate-900 mb-6 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Редагувати категорію</h1>
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" novalidate>
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Назва</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('name') border-red-500 @enderror">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $category->slug) }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('slug') border-red-500 @enderror">
                @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Lucide icon name</label>
                <input type="text" name="icon" value="{{ old('icon', $category->icon) }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('icon') border-red-500 @enderror">
                @error('icon') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Опис</label>
                <textarea name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('description', $category->description) }}</textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="rounded-lg bg-teal-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-teal-700 transition">Зберегти</button>
                <a href="{{ route('admin.categories.index') }}" class="rounded-lg border border-slate-200 text-slate-600 px-5 py-2.5 text-sm font-medium hover:bg-slate-50 transition">Скасувати</a>
            </div>
        </form>
    </div>
</div>
@endsection
