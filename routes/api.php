<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;


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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('jwt.auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::middleware('role:seller')->group(function () {
        Route::get('/inventaris', [InventoryController::class, 'index']);
        Route::post('/inventaris', [InventoryController::class, 'store']);
        Route::get('/inventaris/{inventory}', [InventoryController::class, 'show']);
        Route::put('/inventaris/{inventory}', [InventoryController::class, 'update']);
        Route::delete('/inventaris/{inventory}', [InventoryController::class, 'destroy']);

        Route::get('/pesanan', [OrderController::class, 'index']);
        Route::get('pesanan/{order}', [OrderController::class, 'show']);
        Route::put('pesanan/{order}', [OrderController::class, 'update']);
        Route::delete('pesanan/{order}', [OrderController::class, 'destroy']);
    });
    
    Route::middleware('role:customer')->group(function () {
        Route::post('pesanan/', [OrderController::class, 'store']);
    });
});