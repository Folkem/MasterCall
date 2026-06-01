<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $masters = User::where('role', Role::Master->value)->with('masterProfile.categories')->get();
        $categories = ServiceCategory::all()->keyBy('slug');

        $serviceTemplates = [
            'santehnika' => [
                ['name' => 'Заміна крану', 'price' => 350, 'type' => 'fixed', 'duration' => 60],
                ['name' => 'Усунення засорення', 'price' => 250, 'type' => 'from', 'duration' => 45],
                ['name' => 'Встановлення унітазу', 'price' => 600, 'type' => 'fixed', 'duration' => 90],
                ['name' => 'Ремонт труб', 'price' => 200, 'type' => 'from', 'duration' => 60],
            ],
            'elektryka' => [
                ['name' => 'Встановлення розетки', 'price' => 200, 'type' => 'fixed', 'duration' => 30],
                ['name' => 'Заміна проводки', 'price' => 150, 'type' => 'hourly', 'duration' => null],
                ['name' => 'Встановлення люстри', 'price' => 250, 'type' => 'fixed', 'duration' => 45],
                ['name' => 'Монтаж електрощитка', 'price' => 1500, 'type' => 'from', 'duration' => 180],
            ],
            'maliarne' => [
                ['name' => 'Фарбування кімнати', 'price' => 80, 'type' => 'hourly', 'duration' => null],
                ['name' => 'Шпаклювання стін', 'price' => 60, 'type' => 'hourly', 'duration' => null],
                ['name' => 'Фарбування фасаду', 'price' => 50, 'type' => 'hourly', 'duration' => null],
            ],
            'handyman' => [
                ['name' => 'Збирання меблів', 'price' => 300, 'type' => 'from', 'duration' => 120],
                ['name' => 'Дрібний ремонт', 'price' => 100, 'type' => 'hourly', 'duration' => null],
                ['name' => 'Навішування полиць', 'price' => 150, 'type' => 'fixed', 'duration' => 30],
            ],
            'prybyrannia' => [
                ['name' => 'Генеральне прибирання', 'price' => 800, 'type' => 'from', 'duration' => 240],
                ['name' => 'Прибирання після ремонту', 'price' => 1200, 'type' => 'from', 'duration' => 300],
                ['name' => 'Регулярне прибирання', 'price' => 400, 'type' => 'fixed', 'duration' => 120],
            ],
            'tekhnika' => [
                ['name' => 'Підключення пральної машини', 'price' => 400, 'type' => 'fixed', 'duration' => 60],
                ['name' => 'Встановлення кондиціонера', 'price' => 1800, 'type' => 'from', 'duration' => 180],
                ['name' => 'Налаштування техніки', 'price' => 200, 'type' => 'fixed', 'duration' => 45],
            ],
        ];

        foreach ($masters as $master) {
            $masterCategories = $master->masterProfile?->categories ?? collect();

            foreach ($masterCategories as $category) {
                $templates = $serviceTemplates[$category->slug] ?? [];
                $selected = array_slice($templates, 0, rand(2, min(3, count($templates))));

                foreach ($selected as $template) {
                    Service::firstOrCreate(
                        ['master_id' => $master->id, 'name' => $template['name'], 'category_id' => $category->id],
                        [
                            'description' => null,
                            'price' => $template['price'],
                            'price_type' => $template['type'],
                            'duration_minutes' => $template['duration'],
                        ]
                    );
                }
            }
        }
    }
}
