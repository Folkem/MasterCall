<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = ServiceCategory::withCount('masters')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', 'unique:service_categories,slug'],
            'icon' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Назва обов\'язкова.',
            'slug.required' => 'Slug обов\'язковий.',
            'slug.unique' => 'Такий slug вже існує.',
            'icon.required' => 'Іконка обов\'язкова.',
        ]);

        ServiceCategory::create($request->only('name', 'slug', 'icon', 'description'));

        return back()->with('success', 'Категорію створено.');
    }

    public function edit(ServiceCategory $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, ServiceCategory $category): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', "unique:service_categories,slug,{$category->id}"],
            'icon' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Назва обов\'язкова.',
            'slug.unique' => 'Такий slug вже існує.',
        ]);

        $category->update($request->only('name', 'slug', 'icon', 'description'));

        return redirect()->route('admin.categories.index')->with('success', 'Категорію оновлено.');
    }

    public function destroy(ServiceCategory $category): RedirectResponse
    {
        $category->delete();

        return back()->with('success', 'Категорію видалено.');
    }
}
