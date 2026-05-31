@extends('layouts.public')
@section('title', 'Про нас')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold text-slate-900 mb-6 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">Про MasterCall</h1>
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-8 space-y-4 text-slate-700 leading-relaxed">
        <p>MasterCall — це сучасна платформа для замовлення послуг майстрів додому. Ми об'єднуємо досвідчених фахівців з клієнтами по всій Україні.</p>
        <p>Наша місія — зробити пошук і виклик майстра максимально простим, швидким та надійним. Забудьте про пошук через знайомих чи ненадійні оголошення — на MasterCall тільки перевірені професіонали.</p>
        <h2 class="text-xl font-bold text-slate-800 pt-2" style="font-family: 'Space Grotesk', sans-serif;">Як це працює?</h2>
        <ol class="list-decimal list-inside space-y-2">
            <li>Оберіть категорію або знайдіть майстра за іменем</li>
            <li>Перегляньте профіль майстра, його послуги та відгуки</li>
            <li>Зробіть замовлення із зручною датою та адресою</li>
            <li>Майстер підтвердить замовлення і встановить фінальну ціну</li>
            <li>Оплатіть онлайн та очікуйте майстра</li>
        </ol>
        <p>Ми гарантуємо прозорість ціноутворення та захист ваших інтересів.</p>
    </div>
</div>
@endsection
