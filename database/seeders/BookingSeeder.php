<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\Role;
use App\Models\Booking;
use App\Models\Favorite;
use App\Models\Message;
use App\Models\Review;
use App\Models\User;
use App\Models\WorkReport;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $clients = User::where('role', Role::Client->value)->get();
        $masters = User::where('role', Role::Master->value)->with('masterProfile.categories', 'services')->get();

        if ($clients->isEmpty() || $masters->isEmpty()) {
            return;
        }

        $statusDistribution = [
            OrderStatus::Pending,
            OrderStatus::Pending,
            OrderStatus::Accepted,
            OrderStatus::Accepted,
            OrderStatus::Confirmed,
            OrderStatus::Confirmed,
            OrderStatus::InProgress,
            OrderStatus::InProgress,
            OrderStatus::Completed,
            OrderStatus::Completed,
            OrderStatus::Completed,
            OrderStatus::Completed,
            OrderStatus::Completed,
            OrderStatus::Declined,
            OrderStatus::Cancelled,
        ];

        $addresses = [
            'вул. Хрещатик, 1, Київ',
            'вул. Дмитра Яворницького, 5, Дніпро',
            'вул. Сумська, 12, Харків',
            'вул. Дерибасівська, 7, Одеса',
            'вул. Незалежності, 3, Запоріжжя',
            'вул. Соборна, 15, Рівне',
            'просп. Свободи, 8, Львів',
            'вул. Шевченка, 22, Полтава',
        ];

        $descriptions = [
            'Потрібно замінити кран на кухні, протікає.',
            'Не працює розетка у вітальні.',
            'Хочу пофарбувати спальню, площа 20 кв.м.',
            'Зібрати дитяче ліжко та шафу.',
            'Генеральне прибирання 3-кімнатної квартири.',
            'Підключити пральну машину та посудомийку.',
            'Засорення в ванній кімнаті.',
            'Встановити 3 розетки та вимикачі.',
            'Побілити стелю в кухні.',
            'Прибирання після ремонту.',
        ];

        $bookings = [];

        for ($i = 0; $i < 45; $i++) {
            $client = $clients->random();
            $master = $masters->random();
            $status = $statusDistribution[array_rand($statusDistribution)];

            $masterCategories = $master->masterProfile?->categories ?? collect();
            if ($masterCategories->isEmpty()) {
                continue;
            }
            $category = $masterCategories->random();

            $service = $master->services->where('category_id', $category->id)->first();
            $scheduledAt = now()->subDays(rand(1, 30))->addDays(rand(-5, 30));

            $data = [
                'client_id' => $client->id,
                'master_id' => $master->id,
                'service_id' => $service?->id,
                'category_id' => $category->id,
                'address' => $addresses[array_rand($addresses)],
                'scheduled_at' => $scheduledAt,
                'description' => $descriptions[array_rand($descriptions)],
                'price' => $service ? $service->price : rand(200, 2000),
                'status' => $status,
                'master_note' => in_array($status, [OrderStatus::Declined]) ? 'На жаль, не можу взяти це замовлення.' : null,
                'started_at' => in_array($status, [OrderStatus::InProgress, OrderStatus::Completed]) ? now()->subHours(rand(1, 5)) : null,
                'completed_at' => $status === OrderStatus::Completed ? now()->subHours(rand(0, 3)) : null,
            ];

            $booking = Booking::create($data);
            $bookings[] = $booking;
        }

        // Reviews for completed bookings
        $completedBookings = Booking::where('status', OrderStatus::Completed)->get();
        $reviewCount = 0;

        foreach ($completedBookings as $booking) {
            if ($reviewCount >= 15) {
                break;
            }
            if (Review::where('client_id', $booking->client_id)->where('master_id', $booking->master_id)->exists()) {
                continue;
            }

            $comments = [
                'Чудова робота, дуже задоволений!',
                'Майстер прийшов вчасно, все зробив акуратно.',
                'Рекомендую, якісно і недорого.',
                'Трохи запізнився, але роботу виконав добре.',
                'Відмінний спеціаліст, буду звертатися знову.',
                null,
                'Все чудово, дякую!',
                'Швидко і якісно.',
            ];

            Review::create([
                'client_id' => $booking->client_id,
                'master_id' => $booking->master_id,
                'rating' => rand(3, 5),
                'comment' => $comments[array_rand($comments)],
            ]);
            $reviewCount++;
        }

        // Work reports for completed bookings
        $reportCount = 0;
        foreach ($completedBookings->take(10) as $booking) {
            if ($reportCount >= 10) {
                break;
            }

            WorkReport::firstOrCreate(
                ['booking_id' => $booking->id],
                [
                    'master_id' => $booking->master_id,
                    'client_id' => $booking->client_id,
                    'content' => 'Роботу виконано в повному обсязі. Замінено необхідні деталі, проведено тестування. Все функціонує коректно.',
                ]
            );
            $reportCount++;
        }

        // Messages on some active bookings
        $activeBookings = Booking::whereIn('status', [
            OrderStatus::Accepted,
            OrderStatus::Confirmed,
            OrderStatus::InProgress,
        ])->take(5)->get();

        foreach ($activeBookings as $booking) {
            Message::create([
                'booking_id' => $booking->id,
                'sender_id' => $booking->client_id,
                'body' => 'Доброго дня! Хочу уточнити деталі замовлення.',
                'read_at' => now(),
            ]);
            Message::create([
                'booking_id' => $booking->id,
                'sender_id' => $booking->master_id,
                'body' => 'Доброго дня! Так, готовий обговорити деталі. Коли вам зручно?',
                'read_at' => null,
            ]);
        }

        // Favorites
        $clientsForFav = $clients->take(5);
        foreach ($clientsForFav as $client) {
            $master = $masters->random();
            Favorite::firstOrCreate([
                'client_id' => $client->id,
                'master_id' => $master->id,
            ]);
        }
    }
}
