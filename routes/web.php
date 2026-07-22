<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', function () {
    return redirect()->route('admin.dashboard');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');
    Route::post('/profile/avatar/remove', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/bulk-delete', [UserController::class, 'bulkDestroy'])->name('users.bulk-delete');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');

    Route::get('/properties', [PropertyController::class, 'index'])->name('properties');
    Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
    Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');
    Route::post('/properties/bulk-delete', [PropertyController::class, 'bulkDestroy'])->name('properties.bulk-delete');
    Route::post('/properties/{property}/approve', [PropertyController::class, 'approve'])->name('properties.approve');
    Route::post('/properties/{property}/reject', [PropertyController::class, 'reject'])->name('properties.reject');
    Route::post('/properties/{property}/toggle-featured', [PropertyController::class, 'toggleFeatured'])->name('properties.toggle-featured');
    Route::post('/properties/{property}/units', [PropertyController::class, 'storeUnit'])->name('properties.units.store');
    Route::get('/properties/{property}/units/{unitId}', [PropertyController::class, 'showUnit'])->name('properties.units.show');
    Route::put('/properties/{property}/units/{unitId}', [PropertyController::class, 'updateUnit'])->name('properties.units.update');
    Route::delete('/properties/{property}/units/{unitId}', [PropertyController::class, 'destroyUnit'])->name('properties.units.destroy');

    Route::get('/kyc', [KycController::class, 'index'])->name('kyc');
    Route::post('/kyc', [KycController::class, 'store'])->name('kyc.store');
    Route::get('/kyc/{document}', [KycController::class, 'show'])->name('kyc.show');
    Route::delete('/kyc/{document}', [KycController::class, 'destroy'])->name('kyc.destroy');
    Route::post('/kyc/bulk-delete', [KycController::class, 'bulkDestroy'])->name('kyc.bulk-delete');
    Route::post('/kyc/{document}/approve', [KycController::class, 'approve'])->name('kyc.approve');
    Route::post('/kyc/{document}/reject', [KycController::class, 'reject'])->name('kyc.reject');

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    Route::post('/payments/bulk-delete', [PaymentController::class, 'bulkDestroy'])->name('payments.bulk-delete');
    Route::post('/payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.status');

    Route::get('/subscriptions/plans', [SubscriptionController::class, 'plans'])->name('subscriptions.plans');
    Route::post('/subscriptions/plans', [SubscriptionController::class, 'storePlan'])->name('subscriptions.plans.store');
    Route::put('/subscriptions/plans/{plan}', [SubscriptionController::class, 'updatePlan'])->name('subscriptions.plans.update');
    Route::post('/subscriptions/plans/{plan}/toggle', [SubscriptionController::class, 'togglePlan'])->name('subscriptions.plans.toggle');
    Route::delete('/subscriptions/plans/{plan}', [SubscriptionController::class, 'destroyPlan'])->name('subscriptions.plans.destroy');
    Route::get('/subscriptions', [SubscriptionController::class, 'subscriptions'])->name('subscriptions');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::post('/reports/{report}/resolve', [ReportController::class, 'resolve'])->name('reports.resolve');
    Route::post('/reports/{report}/dismiss', [ReportController::class, 'dismiss'])->name('reports.dismiss');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/toggle', [SettingController::class, 'toggle'])->name('settings.toggle');
});
