<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Campaign Routes
Route::apiResource('campaigns', CampaignController::class);
Route::post('campaigns/{campaign}/approve', [CampaignController::class, 'approve'])->middleware('admin');
Route::post('campaigns/{campaign}/cancel', [CampaignController::class, 'cancel'])->middleware('admin');

// Donation Routes
Route::apiResource('donations', DonationController::class)->only(['index', 'show']);
Route::post('campaigns/{campaign}/donate', [DonationController::class, 'store']);

// Admin Routes
Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard']);
    Route::get('settings', [AdminController::class, 'getSettings']);
    Route::put('settings', [AdminController::class, 'updateSettings']);
    Route::get('users', [AdminController::class, 'getUsers']);
    Route::put('users/{user}/role', [AdminController::class, 'updateUserRole']);
}); 