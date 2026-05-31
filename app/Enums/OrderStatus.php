<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Confirmed = 'confirmed';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Declined = 'declined';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Очікує',
            self::Accepted => 'Прийнято',
            self::Confirmed => 'Підтверджено',
            self::InProgress => 'Виконується',
            self::Completed => 'Завершено',
            self::Declined => 'Відхилено',
            self::Cancelled => 'Скасовано',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-slate-100 text-slate-700',
            self::Accepted => 'bg-teal-100 text-teal-700',
            self::Confirmed => 'bg-blue-100 text-blue-700',
            self::InProgress => 'bg-amber-100 text-amber-700',
            self::Completed => 'bg-emerald-100 text-emerald-700',
            self::Declined => 'bg-red-100 text-red-700',
            self::Cancelled => 'bg-slate-200 text-slate-500',
        };
    }
}
