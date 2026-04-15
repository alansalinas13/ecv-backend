<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\EvaluationController;
use App\Http\Controllers\Api\HospitalController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\UserController;

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

Route::middleware('auth:sanctum')->group(function () {
    ///doctors
    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::get('/doctors/{id}', [DoctorController::class, 'show']);

    Route::middleware('role:1')->group(function () {
        Route::get('/doctor-users/available', [DoctorController::class, 'availableDoctorUsers']);
        Route::post('/doctors', [DoctorController::class, 'store']);
        Route::delete('/doctors/{id}', [DoctorController::class, 'destroy']);
    });

    Route::put('/doctors/{id}', [DoctorController::class, 'update']);

    ////citas
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::get('/appointments/{id}', [AppointmentController::class, 'show']);

    // USER
    Route::middleware('role:3')->post('/appointments', [AppointmentController::class, 'store']);

    // DOCTOR
    Route::middleware('role:2')->put('/appointments/{id}/status', [AppointmentController::class, 'updateStatus']);

    // POSTS
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{id}', [PostController::class, 'show']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    // COMMENTS
    Route::post('/posts/{postId}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

    //evaluaciones
    Route::get('/evaluations', [EvaluationController::class, 'index']);
    Route::post('/evaluations', [EvaluationController::class, 'store']);

    //hospitales
    Route::get('/hospitals', [HospitalController::class, 'index']);
    Route::get('/hospitals/{id}', [HospitalController::class, 'show']);

    Route::middleware('role:1')->group(function () {
        Route::post('/hospitals', [HospitalController::class, 'store']);
        Route::put('/hospitals/{id}', [HospitalController::class, 'update']);
        Route::delete('/hospitals/{id}', [HospitalController::class, 'destroy']);
    });

    //ciudades
    Route::get('/cities', [CityController::class, 'index']);

    Route::middleware('role:1')->group(function () {
        Route::post('/cities', [CityController::class, 'store']);
        Route::put('/cities/{id}', [CityController::class, 'update']);
        Route::delete('/cities/{id}', [CityController::class, 'destroy']);
    });
});

Route::middleware(['auth:sanctum', 'role:1'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});


