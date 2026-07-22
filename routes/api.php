<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\KycController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PropertyImageController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SettingController;
use Illuminate\Support\Facades\Route;

// Auth routes (public)
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Public settings
Route::get('/hero-slides', [SettingController::class, 'heroSlides']);
Route::get('/app-settings', [SettingController::class, 'appSettings']);
Route::get('/categories', [SettingController::class, 'categories']);
Route::get('/regions', [SettingController::class, 'regions']);

// Public properties
Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{property}', [PropertyController::class, 'show']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/password', [AuthController::class, 'changePassword']);

    // Properties (authenticated actions)
    Route::post('/properties', [PropertyController::class, 'store']);
    Route::get('/properties/my', [PropertyController::class, 'myProperties']);
    Route::put('/properties/{property}', [PropertyController::class, 'update']);
    Route::delete('/properties/{property}', [PropertyController::class, 'destroy']);

    // Property Calls
    Route::post('/properties/{property}/call', [PropertyController::class, 'logCall']);
    Route::put('/properties/{property}/call/{call}/end', [PropertyController::class, 'endCall']);

    // Property Units
    Route::get('/properties/{property}/units', [PropertyController::class, 'units']);
    Route::get('/properties/{property}/units/{unitId}', [PropertyController::class, 'showUnit']);
    Route::post('/properties/{property}/units', [PropertyController::class, 'storeUnit']);
    Route::put('/properties/{property}/units/{unitId}', [PropertyController::class, 'updateUnit']);
    Route::delete('/properties/{property}/units/{unitId}', [PropertyController::class, 'destroyUnit']);

    // Property Images
    Route::get('/properties/{property}/images', [PropertyImageController::class, 'index']);
    Route::post('/properties/{property}/images', [PropertyImageController::class, 'upload']);
    Route::put('/properties/{property}/images/reorder', [PropertyImageController::class, 'reorder']);
    Route::put('/properties/{property}/images/{image}/primary', [PropertyImageController::class, 'setPrimary']);
    Route::delete('/properties/{property}/images/{image}', [PropertyImageController::class, 'destroy']);

    // KYC
    Route::get('/kyc', [KycController::class, 'index']);
    Route::get('/kyc/my', [KycController::class, 'myDocuments']);
    Route::get('/kyc/{document}', [KycController::class, 'show']);
    Route::post('/kyc', [KycController::class, 'store']);
    Route::delete('/kyc/{document}', [KycController::class, 'destroy']);
    Route::post('/kyc/{document}/approve', [KycController::class, 'approve']);
    Route::post('/kyc/{document}/reject', [KycController::class, 'reject']);

    // Payments
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::get('/payments/my', [PaymentController::class, 'myPayments']);
    Route::get('/payments/{payment}', [PaymentController::class, 'show']);
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::put('/payments/{payment}/status', [PaymentController::class, 'updateStatus']);
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy']);

    // Subscriptions
    Route::get('/subscriptions/plans', [SubscriptionController::class, 'plans']);
    Route::get('/subscriptions/plans/{plan}', [SubscriptionController::class, 'showPlan']);
    Route::post('/subscriptions/plans', [SubscriptionController::class, 'storePlan']);
    Route::put('/subscriptions/plans/{plan}', [SubscriptionController::class, 'updatePlan']);
    Route::post('/subscriptions/plans/{plan}/toggle', [SubscriptionController::class, 'togglePlan']);
    Route::delete('/subscriptions/plans/{plan}', [SubscriptionController::class, 'destroyPlan']);
    Route::get('/subscriptions/my', [SubscriptionController::class, 'mySubscriptions']);
    Route::post('/subscriptions/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancelSubscription']);

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle']);
    Route::get('/favorites/check/{property}', [FavoriteController::class, 'check']);
    Route::delete('/favorites/{favorite}', [FavoriteController::class, 'destroy']);

    // Reviews
    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::get('/properties/{property}/reviews', [ReviewController::class, 'propertyReviews']);
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy']);
    Route::delete('/notifications', [NotificationController::class, 'destroyAll']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/my', [ReportController::class, 'myReports']);
    Route::get('/reports/{report}', [ReportController::class, 'show']);
    Route::post('/reports', [ReportController::class, 'store']);
    Route::post('/reports/{report}/resolve', [ReportController::class, 'resolve']);
});
