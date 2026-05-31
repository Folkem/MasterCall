@extends('layouts.admin')
@section('title', 'Категорії послуг')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 mb-5 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Категорії послуг</h1>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 uppercase text-xs">
                        <th class="px-4 py-3 text-left">Назва</th>
                        <th class="px-4 py-3 text-left">Slug</th>
                        <th class="px-4 py-3 text-left">Іконка</th>
                        <th class="px-4 py-3 text-right">Майстрів</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($categories as $cat)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $cat->name }}</td>
                        <td class="px-4 py-3 text-slate-500 font-mono text-xs">{{ $cat->slug }}</td>
                        <td class="px-4 py-3">@include('components.icon', ['name' => $cat->icon, 'class' => 'w-5 h-5 text-teal-600'])</td>
                        <td class="px-4 py-3 text-right text-slate-600">{{ $cat->masters_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.categories.edit', $cat) }}" class="text-teal-600 hover:text-teal-800 text-sm font-medium">Редагувати</a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Видалити?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Видалити</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Категорій немає</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <h2 class="text-xl font-bold text-slate-900 mb-5 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Додати категорію</h2>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <form method="POST" action="{{ route('admin.categories.store') }}" novalidate>
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Назва</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('name') border-red-500 @enderror">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug') }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('slug') border-red-500 @enderror">
                    @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Lucide icon name</label>
                    <input type="text" name="icon" value="{{ old('icon', 'tool') }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('icon') border-red-500 @enderror"
                           placeholder="wrench, zap, brush...">
                    @error('icon') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Опис</label>
                    <textarea name="description" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('description') }}</textarea>
                </div>
                <button type="submit" class="rounded-lg bg-teal-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-teal-700 transition">Додати</button>
            </form>
        </div>
    </div>
</div>
@endsection
