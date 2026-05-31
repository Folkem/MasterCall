<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Сантехніка', 'slug' => 'santehnika', 'icon' => 'wrench', 'description' => 'Встановлення та ремонт сантехніки, труб, кранів'],
            ['name' => 'Електрика', 'slug' => 'elektryka', 'icon' => 'zap', 'description' => 'Електромонтаж, ремонт проводки, встановлення розеток'],
            ['name' => 'Малярні роботи', 'slug' => 'maliarne', 'icon' => 'brush', 'description' => 'Фарбування стін, стель, фасадів'],
            ['name' => 'Майстер на всі руки', 'slug' => 'handyman', 'icon' => 'hammer', 'description' => 'Різноманітні дрібні ремонти по будинку'],
            ['name' => 'Прибирання', 'slug' => 'prybyrannia', 'icon' => 'sparkles', 'description' => 'Генеральне та регулярне прибирання'],
            ['name' => 'Встановлення техніки', 'slug' => 'tekhnika', 'icon' => 'plug', 'description' => 'Підключення і налаштування побутової техніки'],
        ];

        foreach ($categories as $data) {
            ServiceCategory::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
