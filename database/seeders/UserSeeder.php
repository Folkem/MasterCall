<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\MasterPhoto;
use App\Models\MasterProfile;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        // photo_file: filename under database/seeders/assets/masters/ (null = no photo)
        // gallery_files: filenames under database/seeders/assets/gallery/ (empty = no gallery)
        $mastersData = [
            ['name' => 'Іван Коваль', 'email' => 'ivan@example.com', 'city' => 'Дніпро', 'bio' => 'Досвідчений сантехнік з 10-річним стажем.', 'years' => 10, 'cats' => ['santehnika'], 'available' => true, 'photo_file' => 'ivan.jpg', 'gallery_files' => ['ivan_g1.jpg', 'ivan_g2.jpg', 'ivan_g3.jpg']],
            ['name' => 'Олег Мельник', 'email' => 'oleg@example.com', 'city' => 'Київ', 'bio' => 'Електрик вищої категорії.', 'years' => 8, 'cats' => ['elektryka'], 'available' => true, 'photo_file' => 'oleg.jpg', 'gallery_files' => ['oleg_g1.jpg', 'oleg_g2.jpg']],
            ['name' => 'Василь Шевченко', 'email' => 'vasyl@example.com', 'city' => 'Харків', 'bio' => 'Займаюся малярними роботами будь-якої складності.', 'years' => 5, 'cats' => ['maliarne'], 'available' => true, 'photo_file' => 'vasyl.jpg', 'gallery_files' => ['vasyl_g1.jpg', 'vasyl_g2.jpg', 'vasyl_g3.jpg', 'vasyl_g4.jpg']],
            ['name' => 'Тарас Бондаренко', 'email' => 'taras@example.com', 'city' => 'Одеса', 'bio' => 'Майстер на всі руки — швидко, якісно, недорого.', 'years' => 7, 'cats' => ['handyman', 'santehnika'], 'available' => true, 'photo_file' => 'taras.jpg', 'gallery_files' => ['taras_g1.jpg', 'taras_g2.jpg', 'taras_g3.jpg']],
            ['name' => 'Микола Гончаренко', 'email' => 'mykola@example.com', 'city' => 'Дніпро', 'bio' => 'Прибираю квартири та офіси.', 'years' => 3, 'cats' => ['prybyrannia'], 'available' => true, 'photo_file' => 'mykola.jpg', 'gallery_files' => []],
            ['name' => 'Андрій Ткаченко', 'email' => 'andriy@example.com', 'city' => 'Київ', 'bio' => 'Підключаю і налаштовую побутову техніку.', 'years' => 6, 'cats' => ['tekhnika', 'elektryka'], 'available' => true, 'photo_file' => 'andriy.jpg', 'gallery_files' => []],
            ['name' => 'Петро Марченко', 'email' => 'petro@example.com', 'city' => 'Харків', 'bio' => 'Сантехнік і електрик в одному флаконі.', 'years' => 12, 'cats' => ['santehnika', 'elektryka'], 'available' => false, 'photo_file' => 'petro.jpg', 'gallery_files' => []],
            ['name' => 'Дмитро Кравченко', 'email' => 'dmytro@example.com', 'city' => 'Київ', 'bio' => 'Роблю ремонт будь-якої складності.', 'years' => 4, 'cats' => ['handyman', 'maliarne'], 'available' => true, 'photo_file' => null, 'gallery_files' => []],
            ['name' => 'Сергій Лисенко', 'email' => 'serhiy@example.com', 'city' => 'Одеса', 'bio' => 'Встановлення кондиціонерів і техніки.', 'years' => 9, 'cats' => ['tekhnika'], 'available' => true, 'photo_file' => null, 'gallery_files' => []],
            ['name' => 'Роман Олійник', 'email' => 'roman@example.com', 'city' => 'Дніпро', 'bio' => 'Комплексне прибирання будинків.', 'years' => 2, 'cats' => ['prybyrannia', 'handyman'], 'available' => true, 'photo_file' => null, 'gallery_files' => []],
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

            if ($profile->photo_path === null && $data['photo_file'] !== null) {
                $profile->update([
                    'photo_path' => $this->seedAssetPhoto('masters', $data['photo_file']),
                ]);
            }

            if ($profile->photos()->count() === 0 && count($data['gallery_files']) > 0) {
                foreach ($data['gallery_files'] as $i => $file) {
                    MasterPhoto::create([
                        'master_profile_id' => $profile->id,
                        'photo_path' => $this->seedAssetPhoto('gallery', $file),
                        'sort_order' => $i,
                    ]);
                }
            }

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

    private function seedAssetPhoto(string $folder, string $filename): string
    {
        $dir = storage_path('app/public/'.$folder);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $dest = $folder.'/'.Str::random(16).'.'.$ext;
        copy(database_path('seeders/assets/'.$folder.'/'.$filename), storage_path('app/public/'.$dest));

        return $dest;
    }
}
