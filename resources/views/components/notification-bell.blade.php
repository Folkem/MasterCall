@auth
@php
    $unreadCount = auth()->user()->unreadNotifications()->count();
    $recentNotifications = auth()->user()->notifications()->limit(5)->get();
@endphp
<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="relative p-2 rounded-lg text-slate-500 hover:bg-slate-100 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 w-4 h-4 rounded-full bg-red-500 text-white text-xs flex items-center justify-center font-bold leading-none">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
        @endif
    </button>
    <div x-show="open" @click.outside="open = false" x-transition
         class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-slate-200 z-50">
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
            <span class="font-semibold text-sm text-slate-700">Сповіщення</span>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.markAllRead') }}">
                    @csrf
                    <button type="submit" class="text-xs text-teal-600 hover:text-teal-800">Позначити всі прочитаними</button>
                </form>
            @endif
        </div>
        <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
            @forelse($recentNotifications as $notification)
                <a href="{{ $notification->data['url'] ?? '#' }}"
                   onclick="fetch('{{ route('notifications.read', $notification->id) }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token()}}','Content-Type':'application/json'}})"
                   class="block px-4 py-3 hover:bg-slate-50 {{ is_null($notification->read_at) ? 'bg-teal-50' : '' }}">
                    <p class="text-sm text-slate-700">{{ $notification->data['message'] ?? 'Сповіщення' }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                </a>
            @empty
                <div class="px-4 py-6 text-center text-sm text-slate-400">Немає сповіщень</div>
            @endforelse
        </div>
    </div>
</div>
@endauth
