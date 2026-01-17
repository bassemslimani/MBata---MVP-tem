<?php

use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Admin\AdminPropertyController;
use App\Http\Controllers\Api\Admin\AdminReservationController;
use App\Http\Controllers\Api\Admin\AdminReviewController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\Admin\SuperAdminController;
use App\Http\Controllers\Api\AmenityController;
use App\Http\Controllers\Api\Client\DashboardController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\Client\FavoriteController;
use App\Http\Controllers\Api\Client\ReservationController;
use App\Http\Controllers\Api\Client\ReviewController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\PropertyCategoryController;
use App\Http\Controllers\Api\PropertyTypeController;
use App\Http\Controllers\Api\WilayaController;
use App\Http\Controllers\Api\Owner\AvailabilityController;
use App\Http\Controllers\Api\Owner\OwnerPropertyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // Localization endpoints (public)
    Route::get('/wilayas', [WilayaController::class, 'index']);
    Route::get('/wilayas/{id}/communes', [WilayaController::class, 'communes']);
    Route::get('/currencies/exchange-rates', [CurrencyController::class, 'exchangeRates']);
    Route::get('/currencies', [CurrencyController::class, 'index']);

    // Property reference data (public)
    Route::get('/property-types', [PropertyTypeController::class, 'index']);
    Route::get('/property-categories', [PropertyCategoryController::class, 'index']);
    Route::get('/amenities', [AmenityController::class, 'index']);

    // Properties search & details (public)
    Route::get('/properties', [PropertyController::class, 'index']);
    Route::get('/properties/{id}', [PropertyController::class, 'show']);
    Route::get('/properties/{id}/reviews', [PropertyController::class, 'reviews']);
    Route::get('/properties/{id}/availability', [PropertyController::class, 'availability']);

    // Client endpoints (authentication required)
    Route::middleware('auth:sanctum')->prefix('client')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Reservations
        Route::get('/reservations', [ReservationController::class, 'index']);
        Route::post('/reservations', [ReservationController::class, 'store']);
        Route::get('/reservations/{id}', [ReservationController::class, 'show']);
        Route::post('/reservations/{id}/cancel', [ReservationController::class, 'cancel']);

        // Reviews
        Route::get('/reviews', [ReviewController::class, 'index']);
        Route::post('/reviews', [ReviewController::class, 'store']);
        Route::get('/reviews/{id}', [ReviewController::class, 'show']);

        // Favorites
        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/favorites', [FavoriteController::class, 'store']);
        Route::delete('/favorites/{id}', [FavoriteController::class, 'destroy']);
    });

    // Owner endpoints (authentication required)
    Route::middleware('auth:sanctum')->prefix('owner')->group(function () {
        // Properties CRUD
        Route::get('/properties', [OwnerPropertyController::class, 'index']);
        Route::post('/properties', [OwnerPropertyController::class, 'store']);
        Route::get('/properties/{id}', [OwnerPropertyController::class, 'show']);
        Route::put('/properties/{id}', [OwnerPropertyController::class, 'update']);
        Route::patch('/properties/{id}', [OwnerPropertyController::class, 'update']);
        Route::delete('/properties/{id}', [OwnerPropertyController::class, 'destroy']);

        // Property Images
        Route::post('/properties/{id}/images', [OwnerPropertyController::class, 'uploadImage']);
        Route::delete('/properties/{propertyId}/images/{imageId}', [OwnerPropertyController::class, 'deleteImage']);
        Route::put('/properties/{propertyId}/images/{imageId}/primary', [OwnerPropertyController::class, 'setPrimaryImage']);

        // Property Availabilities
        Route::get('/properties/{propertyId}/availabilities', [AvailabilityController::class, 'index']);
        Route::post('/properties/{propertyId}/availabilities', [AvailabilityController::class, 'store']);
        Route::put('/properties/{propertyId}/availabilities/{id}', [AvailabilityController::class, 'update']);
        Route::patch('/properties/{propertyId}/availabilities/{id}', [AvailabilityController::class, 'update']);
        Route::delete('/properties/{propertyId}/availabilities/{id}', [AvailabilityController::class, 'destroy']);
    });

    // Admin endpoints (authentication + admin role required)
    Route::middleware(['auth:sanctum', 'isAdmin'])->prefix('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        // Users management
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::get('/users/{id}', [AdminUserController::class, 'show']);
        Route::post('/users/{id}/toggle-status', [AdminUserController::class, 'toggleStatus']);
        Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);

        // Properties management
        Route::get('/properties', [AdminPropertyController::class, 'index']);
        Route::get('/properties/{id}', [AdminPropertyController::class, 'show']);
        Route::post('/properties/{id}/verify', [AdminPropertyController::class, 'verify']);
        Route::delete('/properties/{id}', [AdminPropertyController::class, 'destroy']);

        // Reservations management
        Route::get('/reservations', [AdminReservationController::class, 'index']);
        Route::get('/reservations/{id}', [AdminReservationController::class, 'show']);

        // Reviews management
        Route::get('/reviews', [AdminReviewController::class, 'index']);
        Route::get('/reviews/{id}', [AdminReviewController::class, 'show']);
        Route::post('/reviews/{id}/toggle-visibility', [AdminReviewController::class, 'toggleVisibility']);
        Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy']);
    });

    // Super Admin endpoints (super_admin role only)
    Route::middleware(['auth:sanctum'])->prefix('super-admin')->group(function () {
        Route::get('/admins', [SuperAdminController::class, 'indexAdmins']);
        Route::post('/admins', [SuperAdminController::class, 'storeAdmin']);
        Route::delete('/admins/{id}', [SuperAdminController::class, 'destroyAdmin']);
    });
});
