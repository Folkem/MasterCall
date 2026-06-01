<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = Service::with('category')
            ->where('master_id', auth()->id())
            ->get();
        $categories = ServiceCategory::all();

        return view('cabinet.services.index', compact('services', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:service_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'price_type' => ['required', 'in:fixed,from,hourly'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
        ], [
            'category_id.required' => 'Категорія обов\'язкова.',
            'name.required' => 'Назва послуги обов\'язкова.',
            'price.required' => 'Ціна обов\'язкова.',
            'price_type.required' => 'Тип ціни обов\'язковий.',
        ]);

        Service::create(array_merge($validated, ['master_id' => auth()->id()]));

        return back()->with('success', 'Послугу додано.');
    }

    public function edit(Service $service): View
    {
        abort_unless($service->master_id === auth()->id(), 403);
        $categories = ServiceCategory::all();

        return view('cabinet.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        abort_unless($service->master_id === auth()->id(), 403);

        $validated = $request->validate([
            'category_id' => ['required', 'exists:service_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'price_type' => ['required', 'in:fixed,from,hourly'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
        ], [
            'category_id.required' => 'Категорія обов\'язкова.',
            'name.required' => 'Назва послуги обов\'язкова.',
            'price.required' => 'Ціна обов\'язкова.',
            'price_type.required' => 'Тип ціни обов\'язковий.',
        ]);

        $service->update($validated);

        return redirect()->route('cabinet.services.index')->with('success', 'Послугу оновлено.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        abort_unless($service->master_id === auth()->id(), 403);

        $service->delete();

        return back()->with('success', 'Послугу видалено.');
    }
}
