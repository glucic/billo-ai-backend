<?php

use App\Models\User;
use App\Http\Controllers\OrganisationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/users', function (Request $request) {
    return User::all();
});

Route::apiResource('organisations', OrganisationController::class);