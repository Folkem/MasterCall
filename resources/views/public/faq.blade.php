@extends('layouts.public')
@section('title', 'Питання та відповіді')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold text-slate-900 mb-6 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Часті питання</h1>
    <div class="space-y-4">
        @foreach([
            ['q' => 'Як замовити майстра?', 'a' => 'Зареєструйтесь як клієнт, знайдіть підходящого майстра у каталозі, оберіть послугу та натисніть «Замовити». Вкажіть адресу, дату та час.'],
            ['q' => 'Як відбувається оплата?', 'a' => 'Оплата здійснюється онлайн через Stripe після того, як майстер прийме ваше замовлення та підтвердить фінальну ціну.'],
            ['q' => 'Чи можна скасувати замовлення?', 'a' => 'Так, ви можете скасувати замовлення зі статусами «Очікує», «Прийнято» або «Підтверджено» до запланованої дати виконання.'],
            ['q' => 'Як залишити відгук?', 'a' => 'Після завершення роботи ви можете залишити відгук на сторінці замовлення або на сторінці майстра. Відгук доступний лише після виконаного замовлення.'],
            ['q' => 'Як стати майстром на платформі?', 'a' => 'Зверніться до адміністрації. Майстерські профілі створюються адміністратором після верифікації спеціаліста.'],
            ['q' => 'Що робити, якщо майстер не з\'явився?', 'a' => 'Зверніться до служби підтримки через сторінку «Контакти». Ми розберемося в ситуації.'],
        ] as $item)
        <div x-data="{ open: false }" class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <button @click="open = !open" class="w-full flex items-center justify-between px-5 py-4 text-left">
                <span class="font-medium text-slate-800">{{ $item['q'] }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" :class="open ? 'rotate-180' : ''" class="w-5 h-5 text-slate-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div x-show="open" x-transition class="px-5 pb-4 text-slate-600 text-sm leading-relaxed border-t border-slate-100 pt-3">
                {{ $item['a'] }}
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
