<?php

use App\Http\Controllers\Account\FavoriteController;
use App\Http\Controllers\Account\OrderController as AccountOrderController;
use App\Http\Controllers\Account\PaymentController;
use App\Http\Controllers\Account\ProfileController as AccountProfileController;
use App\Http\Controllers\Account\ReviewController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MasterController as AdminMasterController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Cabinet\OrderController as CabinetOrderController;
use App\Http\Controllers\Cabinet\ProfileController as CabinetProfileController;
use App\Http\Controllers\Cabinet\ServiceController as CabinetServiceController;
use App\Http\Controllers\Cabinet\WorkReportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Public\MasterController;
use App\Http\Controllers\Public\StaticController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/masters', [MasterController::class, 'index'])->name('masters.index');
Route::get('/masters/{master}', [MasterController::class, 'show'])->name('masters.show');
Route::get('/about', [StaticController::class, 'about'])->name('about');
Route::get('/faq', [StaticController::class, 'faq'])->name('faq');
Route::get('/contacts', [StaticController::class, 'contacts'])->name('contacts');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Notifications
Route::middleware('auth')->group(function () {
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
});

// Account (client)
Route::middleware(['auth', 'role:client'])->prefix('account')->name('account.')->group(function () {
    Route::get('/profile', [AccountProfileController::class, 'edit'])->name('profile');
    Route::post('/profile', [AccountProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [AccountProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/orders', [AccountOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [AccountOrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [AccountOrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [AccountOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [AccountOrderController::class, 'cancel'])->name('orders.cancel');

    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites');
    Route::post('/favorites/{master}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    Route::get('/reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

// Checkout
Route::middleware(['auth', 'role:client'])->prefix('checkout')->name('checkout.')->group(function () {
    Route::post('/orders/{order}', [PaymentController::class, 'checkout'])->name('pay');
    Route::get('/success/{order}', [PaymentController::class, 'success'])->name('success');
    Route::get('/cancel/{order}', [PaymentController::class, 'cancel'])->name('cancel');
});

// Messages (shared between client and master)
Route::middleware('auth')->group(function () {
    Route::post('/orders/{order}/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/orders/{order}/messages/poll', [MessageController::class, 'poll'])->name('messages.poll');
});

// Cabinet (master)
Route::middleware(['auth', 'role:master'])->prefix('cabinet')->name('cabinet.')->group(function () {
    Route::get('/orders', [CabinetOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [CabinetOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/accept', [CabinetOrderController::class, 'accept'])->name('orders.accept');
    Route::post('/orders/{order}/decline', [CabinetOrderController::class, 'decline'])->name('orders.decline');
    Route::post('/orders/{order}/start', [CabinetOrderController::class, 'start'])->name('orders.start');
    Route::post('/orders/{order}/complete', [CabinetOrderController::class, 'complete'])->name('orders.complete');
    Route::post('/orders/{order}/report', [WorkReportController::class, 'store'])->name('orders.report.store');

    Route::get('/services', [CabinetServiceController::class, 'index'])->name('services.index');
    Route::post('/services', [CabinetServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}/edit', [CabinetServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [CabinetServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [CabinetServiceController::class, 'destroy'])->name('services.destroy');

    Route::get('/profile', [CabinetProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [CabinetProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photos', [CabinetProfileController::class, 'storePhoto'])->name('profile.photos.store');
    Route::delete('/profile/photos/{photo}', [CabinetProfileController::class, 'deletePhoto'])->name('profile.photos.delete');
});

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/masters', [AdminMasterController::class, 'index'])->name('masters.index');
    Route::get('/masters/create', [AdminMasterController::class, 'create'])->name('masters.create');
    Route::post('/masters', [AdminMasterController::class, 'store'])->name('masters.store');
    Route::get('/masters/{master}/edit', [AdminMasterController::class, 'edit'])->name('masters.edit');
    Route::put('/masters/{master}', [AdminMasterController::class, 'update'])->name('masters.update');
    Route::post('/masters/{master}/toggle-active', [AdminMasterController::class, 'toggleActive'])->name('masters.toggleActive');
    Route::delete('/masters/photos/{photo}', [AdminMasterController::class, 'deletePhoto'])->name('masters.photos.delete');

    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients/{client}/toggle-active', [ClientController::class, 'toggleActive'])->name('clients.toggleActive');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
});
