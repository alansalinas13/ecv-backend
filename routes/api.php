<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::get('/test', function () {
    return response()->json(['message' => 'API funcionando']);
});
////test para autorizaciones
Route::middleware(['auth:sanctum', 'role:1'])->get('/admin-test', function () {
    return response()->json(['message' => 'Acceso admin']);
});

Route::middleware(['auth:sanctum', 'role:2'])->get('/doctor-test', function () {
    return response()->json(['message' => 'Acceso doctor']);
});

Route::middleware(['auth:sanctum', 'role:3'])->get('/user-test', function () {
    return response()->json(['message' => 'Acceso usuario']);
});

Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

});
