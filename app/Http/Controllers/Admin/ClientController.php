<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', Role::Client->value)->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $clients = $query->paginate(20)->withQueryString();

        return view('admin.clients.index', compact('clients'));
    }

    public function toggleActive(User $client): RedirectResponse
    {
        abort_unless($client->isClient(), 404);

        $client->update(['is_active' => ! $client->is_active]);
        $status = $client->is_active ? 'активовано' : 'деактивовано';

        return back()->with('success', "Клієнта {$status}.");
    }
}
