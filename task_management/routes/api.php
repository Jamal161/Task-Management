<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);




use App\Http\Controllers\AdminController;
use App\Http\Controllers\TaskController;


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/tasks/{task}', [TaskController::class, 'show']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
    
    Route::middleware('admin')->group(function () {
        Route::get('/admin/users', [AdminController::class, 'indexUsers']);
        Route::get('/admin/users/{userId}/tasks', [AdminController::class, 'indexUserTasks']);
        Route::post('/admin/users', [AdminController::class, 'storeUser']);
        
        Route::put('/admin/users/{id}', [AdminController::class, 'updateUser']);
        
        Route::delete('/admin/users/{userId}', [AdminController::class, 'destroyUser']);
        Route::post('/admin/users/{userId}/tasks', [AdminController::class, 'storeUserTask']);
        
        Route::put('/admin/users/{userId}/tasks/{task}', [AdminController::class, 'updateTask']);
        
        Route::delete('/admin/tasks/{task}', [AdminController::class, 'destroyTask']);
    });
});



Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin-dashboard', [AdminController::class, 'dashboard']);
});

Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::get('/user-dashboard', [UserController::class, 'dashboard']);
});




