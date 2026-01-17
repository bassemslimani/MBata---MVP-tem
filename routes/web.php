<?php

use App\Http\Controllers\Admin\CurrencySettingsController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PropertyManagementController;
use App\Http\Controllers\Admin\ReservationManagementController;
use App\Http\Controllers\Admin\ReviewManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\ReservationController;
use App\Http\Controllers\Client\FavoriteController;
use App\Http\Controllers\Client\ProfileController as ClientProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SuperAdmin\AdminManagementController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home page
Route::get('/', function () {
    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('home');

// Search/Properties
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/properties/{id}', [PropertyController::class, 'show'])->name('properties.show');
Route::get('/properties/{id}/book', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

// Client routes (authentication required)
Route::middleware(['auth', 'verified'])->prefix('client')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('client.dashboard');

    // Reservations
    Route::get('/reservations', [ReservationController::class, 'index'])->name('client.reservations');
    Route::get('/reservations/{id}', [ReservationController::class, 'show'])->name('client.reservations.show');
    Route::post('/reservations/{id}/cancel', [ReservationController::class, 'cancel'])->name('client.reservations.cancel');
    Route::get('/reservations/{id}/invoice', [ReservationController::class, 'invoice'])->name('client.reservations.invoice');

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('client.favorites');

    // Profile
    Route::get('/profile', [ClientProfileController::class, 'edit'])->name('client.profile.edit');
    Route::put('/profile', [ClientProfileController::class, 'update'])->name('client.profile.update');
    Route::put('/password', [ClientProfileController::class, 'updatePassword'])->name('client.password.update');
    Route::put('/preferences', [ClientProfileController::class, 'updatePreferences'])->name('client.preferences.update');
});

// Owner routes (authentication required)
Route::middleware(['auth', 'verified'])->prefix('owner')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Owner/Dashboard');
    })->name('owner.dashboard');

    Route::get('/properties', function () {
        return Inertia::render('Owner/Properties/Index');
    })->name('owner.properties');

    Route::get('/properties/create', function () {
        return Inertia::render('Owner/Properties/Create');
    })->name('owner.properties.create');

    Route::get('/properties/{id}', function ($id) {
        return Inertia::render('Owner/Properties/Show', ['propertyId' => $id]);
    })->name('owner.properties.show');

    Route::get('/properties/{id}/edit', function ($id) {
        return Inertia::render('Owner/Properties/Edit', ['propertyId' => $id]);
    })->name('owner.properties.edit');

    Route::get('/properties/{id}/photos', function ($id) {
        return Inertia::render('Owner/Properties/Photos', ['propertyId' => $id]);
    })->name('owner.properties.photos');

    Route::get('/properties/{id}/availability', function ($id) {
        return Inertia::render('Owner/Properties/Availability', ['propertyId' => $id]);
    })->name('owner.properties.availability');

    Route::get('/reservations', function () {
        return Inertia::render('Owner/Reservations/Index');
    })->name('owner.reservations');

    Route::get('/reviews', function () {
        return Inertia::render('Owner/Reviews/Index');
    })->name('owner.reviews');

    Route::get('/earnings', function () {
        return Inertia::render('Owner/Earnings/Index');
    })->name('owner.earnings');
});

// Admin routes (authentication + admin role required)
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [UserManagementController::class, 'index'])->name('admin.users');
    Route::get('/users/{id}', [UserManagementController::class, 'show'])->name('admin.users.show');
    Route::get('/properties', [PropertyManagementController::class, 'index'])->name('admin.properties');
    Route::get('/properties/{id}', [PropertyManagementController::class, 'show'])->name('admin.properties.show');
    Route::get('/reservations', [ReservationManagementController::class, 'index'])->name('admin.reservations');
    Route::get('/reservations/{id}', [ReservationManagementController::class, 'show'])->name('admin.reservations.show');
    Route::get('/reviews', [ReviewManagementController::class, 'index'])->name('admin.reviews');
    Route::get('/reviews/{id}', [ReviewManagementController::class, 'show'])->name('admin.reviews.show');
    Route::get('/settings/currency', [CurrencySettingsController::class, 'edit'])->name('admin.settings.currency');
});

// Super Admin routes (authentication + super_admin role required)
Route::middleware(['auth', 'verified'])->prefix('super-admin')->group(function () {
    Route::get('/admins', [AdminManagementController::class, 'index'])->name('super-admin.admins');
});

// Default dashboard route
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authentication routes (profile management)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
