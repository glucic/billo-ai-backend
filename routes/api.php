<?php

use App\Models\User;
use App\Http\Controllers\OrganisationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('invoices', controller: InvoiceController::class);

    Route::get('/users', function (Request $request) {
        return User::all();
    });

    Route::apiResource('organisations', OrganisationController::class);
    Route::post('organisations/{organisation}/join', [OrganisationController::class, 'join']);
    Route::post('organisations/{organisation}/leave', [OrganisationController::class, 'leave']);
});