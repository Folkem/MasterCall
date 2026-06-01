@extends('layouts.account')
@section('title', 'Замовлення #' . $order->id)

@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('account.orders.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-teal-700 mb-6 transition">
        @include('components.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4'])
        До замовлень
    </a>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        {{-- Details --}}
        <div class="md:col-span-2 space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-xl font-bold text-slate-900" style="font-family: 'Space Grotesk', sans-serif;">Замовлення #{{ $order->id }}</h1>
                    <x-status-badge :status="$order->status" />
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Майстер</span>
                        <a href="{{ route('masters.show', $order->master) }}" class="font-medium text-teal-700 hover:underline">{{ $order->master->name }}</a>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Категорія</span>
                        <span class="font-medium text-slate-800">{{ $order->category->name }}</span>
                    </div>
                    @if($order->service)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Послуга</span>
                        <span class="font-medium text-slate-800">{{ $order->service->name }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-slate-500">Дата</span>
                        <span class="font-medium text-slate-800">{{ $order->scheduled_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Адреса</span>
                        <span class="font-medium text-slate-800 text-right max-w-xs">{{ $order->address }}</span>
                    </div>
                    @if($order->price)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Ціна</span>
                        <span class="font-bold text-teal-700">{{ number_format($order->price, 0, '.', ' ') }} грн</span>
                    </div>
                    @endif
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100">
                    <p class="text-xs text-slate-500 mb-1">Опис</p>
                    <p class="text-sm text-slate-700">{{ $order->description }}</p>
                </div>

                @if($order->master_note)
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <p class="text-xs text-slate-500 mb-1">Примітка майстра</p>
                    <p class="text-sm text-slate-700">{{ $order->master_note }}</p>
                </div>
                @endif
            </div>

            {{-- Actions --}}
            @if($order->status->value === 'accepted')
            <div class="bg-teal-50 border border-teal-200 rounded-xl p-4">
                <p class="text-sm text-teal-700 font-medium mb-3">Майстер прийняв замовлення. Сума: <strong>{{ number_format($order->price, 0, '.', ' ') }} грн</strong></p>
                <form method="POST" action="{{ route('checkout.pay', $order) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-teal-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-teal-700 transition">
                        @include('components.icon', ['name' => 'credit-card', 'class' => 'w-4 h-4'])
                        Сплатити
                    </button>
                </form>
            </div>
            @endif

            @if($order->status->value === 'completed' && $order->workReport)
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                <h3 class="font-semibold text-slate-800 mb-2" style="font-family: 'Space Grotesk', sans-serif;">Звіт майстра</h3>
                <p class="text-sm text-slate-700">{{ $order->workReport->content }}</p>
            </div>
            @endif

            @if($order->status->value === 'completed')
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-slate-800" style="font-family: 'Space Grotesk', sans-serif;">Відгук</h3>
                    <a href="{{ route('account.reviews.create', ['master' => $order->master_id]) }}"
                       class="text-sm text-teal-600 hover:text-teal-800 font-medium">
                        {{ $existingReview ? 'Редагувати' : 'Залишити відгук' }}
                    </a>
                </div>
                @if($existingReview)
                    <div class="flex items-center gap-1 mb-1">
                        @for($i = 1; $i <= 5; $i++)
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 {{ $i <= $existingReview->rating ? 'text-yellow-400 fill-yellow-400' : 'text-slate-300' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        @endfor
                    </div>
                    @if($existingReview->comment)
                        <p class="text-sm text-slate-600">{{ $existingReview->comment }}</p>
                    @endif
                @else
                    <p class="text-sm text-slate-400">Відгук ще не залишено</p>
                @endif
            </div>
            @endif

            @if($order->canBeCancelledByClient())
            <form method="POST" action="{{ route('account.orders.cancel', $order) }}" onsubmit="return confirm('Скасувати замовлення?')">
                @csrf
                <button type="submit" class="rounded-lg border border-red-300 text-red-600 px-4 py-2 text-sm font-medium hover:bg-red-50 transition">
                    Скасувати замовлення
                </button>
            </form>
            @endif
        </div>

        {{-- Chat --}}
        <div class="md:col-span-1">
            <div x-data="{
                    poll() {
                        fetch('{{ route('messages.poll', $order) }}')
                            .then(r => r.text())
                            .then(html => {
                                this.$refs.list.innerHTML = html;
                                this.$refs.list.scrollTop = this.$refs.list.scrollHeight;
                            });
                    }
                }" x-init="$refs.list.scrollTop = $refs.list.scrollHeight; setInterval(() => poll(), 10000)"
                class="bg-white rounded-xl shadow-sm border border-slate-100 flex flex-col overflow-hidden" style="height: 500px;" id="chat">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="font-semibold text-sm text-slate-800">Чат з майстром</h3>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-3" x-ref="list" id="messages-list">
                    @include('partials.messages-list', ['messages' => $order->messages])
                </div>
                <div class="p-3 border-t border-slate-100 shrink-0">
                    <form method="POST" action="{{ route('messages.store', $order) }}" class="flex gap-2">
                        @csrf
                        <input type="text" name="body" placeholder="Написати..." required
                               class="flex-1 min-w-0 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <button type="submit" class="shrink-0 rounded-lg bg-teal-600 text-white p-2 hover:bg-teal-700 transition">
                            @include('components.icon', ['name' => 'send', 'class' => 'w-4 h-4'])
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
