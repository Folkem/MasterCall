<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\MasterProfile;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Адміністратор', 'password' => Hash::make('password'), 'role' => Role::Admin, 'is_active' => true]
        );

        $categories = ServiceCategory::all()->keyBy('slug');

        $mastersData = [
            ['name' => 'Іван Коваль', 'email' => 'ivan@example.com', 'city' => 'Дніпро', 'bio' => 'Досвідчений сантехнік з 10-річним стажем.', 'years' => 10, 'cats' => ['santehnika'], 'available' => true],
            ['name' => 'Олег Мельник', 'email' => 'oleg@example.com', 'city' => 'Київ', 'bio' => 'Електрик вищої категорії.', 'years' => 8, 'cats' => ['elektryka'], 'available' => true],
            ['name' => 'Василь Шевченко', 'email' => 'vasyl@example.com', 'city' => 'Харків', 'bio' => 'Займаюся малярними роботами будь-якої складності.', 'years' => 5, 'cats' => ['maliarne'], 'available' => true],
            ['name' => 'Тарас Бондаренко', 'email' => 'taras@example.com', 'city' => 'Одеса', 'bio' => 'Майстер на всі руки — швидко, якісно, недорого.', 'years' => 7, 'cats' => ['handyman', 'santehnika'], 'available' => true],
            ['name' => 'Микола Гончаренко', 'email' => 'mykola@example.com', 'city' => 'Дніпро', 'bio' => 'Прибираю квартири та офіси.', 'years' => 3, 'cats' => ['prybyrannia'], 'available' => true],
            ['name' => 'Андрій Ткаченко', 'email' => 'andriy@example.com', 'city' => 'Київ', 'bio' => 'Підключаю і налаштовую побутову техніку.', 'years' => 6, 'cats' => ['tekhnika', 'elektryka'], 'available' => true],
            ['name' => 'Петро Марченко', 'email' => 'petro@example.com', 'city' => 'Харків', 'bio' => 'Сантехнік і електрик в одному флаконі.', 'years' => 12, 'cats' => ['santehnika', 'elektryka'], 'available' => false],
            ['name' => 'Дмитро Кравченко', 'email' => 'dmytro@example.com', 'city' => 'Київ', 'bio' => 'Роблю ремонт будь-якої складності.', 'years' => 4, 'cats' => ['handyman', 'maliarne'], 'available' => true],
            ['name' => 'Сергій Лисенко', 'email' => 'serhiy@example.com', 'city' => 'Одеса', 'bio' => 'Встановлення кондиціонерів і техніки.', 'years' => 9, 'cats' => ['tekhnika'], 'available' => true],
            ['name' => 'Роман Олійник', 'email' => 'roman@example.com', 'city' => 'Дніпро', 'bio' => 'Комплексне прибирання будинків.', 'years' => 2, 'cats' => ['prybyrannia', 'handyman'], 'available' => true],
        ];

        foreach ($mastersData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => Role::Master,
                    'is_active' => true,
                    'phone' => '+380'.rand(500000000, 999999999),
                ]
            );

            $profile = MasterProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'bio' => $data['bio'],
                    'city' => $data['city'],
                    'years_experience' => $data['years'],
                    'is_available' => $data['available'],
                ]
            );

            $catIds = collect($data['cats'])
                ->map(fn ($slug) => $categories->get($slug)?->id)
                ->filter()
                ->toArray();

            $profile->categories()->sync($catIds);
        }

        // Clients
        $clientNames = [
            'Марина Петренко', 'Олена Іваненко', 'Наталія Сидоренко', 'Катерина Мороз',
            'Юлія Кузьменко', 'Ірина Поліщук', 'Тетяна Яковенко', 'Алла Романенко',
            'Оксана Пономаренко', 'Людмила Харченко', 'Борис Степаненко', 'Геннадій Власенко',
            'Анатолій Білоус', 'Леонід Гриценко', 'Федір Назаренко', 'Григорій Павленко',
            'Валентин Захаренко', 'Микита Данченко', 'Артем Рибаченко', 'Євген Колісниченко',
            'Максим Тимченко', 'Іван Дяченко', 'Олексій Пилипенко', 'Сергій Савченко', 'Юрій Демченко',
        ];

        foreach ($clientNames as $i => $name) {
            $email = 'client'.($i + 1).'@example.com';
            User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role' => Role::Client,
                    'is_active' => true,
                    'phone' => '+380'.rand(500000000, 999999999),
                ]
            );
        }
    }
}
