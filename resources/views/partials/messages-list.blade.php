@forelse($messages as $msg)
<div class="{{ $msg->sender_id === auth()->id() ? 'flex justify-end' : 'flex justify-start' }}">
    <div class="max-w-xs rounded-lg px-3 py-2 text-sm {{ $msg->sender_id === auth()->id() ? 'bg-teal-600 text-white' : 'bg-slate-100 text-slate-700' }}">
        <p>{{ $msg->body }}</p>
        <p class="text-xs mt-1 opacity-60">{{ $msg->formattedTime() }}</p>
    </div>
</div>
@empty
<div class="text-center text-slate-400 text-xs py-4">Повідомлень ще немає</div>
@endforelse
