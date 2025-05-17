<?php

use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Public Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Add the Group routes here:
    Route::apiResource('groups', GroupController::class);
    Route::apiResource('bills', BillController::class);
    Route::apiResource('transactions', TransactionController::class);

});
