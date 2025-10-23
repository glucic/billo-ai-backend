<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrganisationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('invoices', controller: InvoiceController::class);
    Route::apiResource('organisations', OrganisationController::class);
    Route::post('organisations/{organisation}/join', [OrganisationController::class, 'join']);
    Route::post('organisations/{organisation}/leave', [OrganisationController::class, 'leave']);
    
    Route::middleware('api.rateLimit')->prefix('location')->group(function () {
        Route::get('/suggestions', [LocationController::class, 'suggestions']);
        Route::get('/details', [LocationController::class, 'details']);
    });
});