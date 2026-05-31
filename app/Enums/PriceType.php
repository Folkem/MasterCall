<?php

namespace App\Enums;

enum PriceType: string
{
    case Fixed = 'fixed';
    case From = 'from';
    case Hourly = 'hourly';

    public function label(): string
    {
        return match ($this) {
            self::Fixed => 'фіксована',
            self::From => 'від',
            self::Hourly => 'за годину',
        };
    }

    public function priceDisplay(float $price): string
    {
        $formatted = number_format($price, 0, '.', ' ');

        return match ($this) {
            self::Fixed => "{$formatted} грн",
            self::From => "від {$formatted} грн",
            self::Hourly => "{$formatted} грн/год",
        };
    }
}
