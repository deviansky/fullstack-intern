<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\ActivityLogController;

//Login
Route::post('/login', [AuthController::class, 'login']);

// Rute Autentikasi
Route::middleware('auth:sanctum')->group(function () {
    // User
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);

    // Task
    Route::get('/tasks', [TaskController::class, 'index']); 
    Route::post('/tasks', [TaskController::class, 'store']); 
    Route::put('/tasks/{task}', [TaskController::class, 'update']); 
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']); 

    // Activity Log
    Route::get('/logs', [ActivityLogController::class, 'index']); 
});