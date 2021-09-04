<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

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

Route::post('login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => 'auth:sanctum'], function() {

	
	Route::get('/role-grid', [RoleController::class, 'grid']);
	Route::get('/role/{id}', [RoleController::class, 'show']);
	Route::get('/role-permissions', [RoleController::class, 'getPermission']);
	Route::post('/role', [RoleController::class, 'store']);
	Route::put('/role/{id}', [RoleController::class, 'update']);
	Route::delete('/role/{id}',  [RoleController::class, 'destroy']);
	
	Route::get('/user-grid', [UserController::class, 'grid']);
	Route::get('/user/{id}', [UserController::class, 'show']);
	Route::post('/user', [UserController::class, 'store']);
	Route::put('/user/{id}', [UserController::class, 'update']);
	Route::put('/user/{id}/change-password', [UserController::class, 'changePassword']);
	Route::put('/user/{id}/change-photo', [UserController::class, 'changePhoto']);
	Route::delete('/user/{id}',  [UserController::class, 'destroy']);

	Route::get('/profile/{id}', [ProfileController::class, 'show']);
	Route::put('/profile/{id}', [ProfileController::class, 'update']);
	Route::put('/profile/{id}/change-password', [ProfileController::class, 'updateProfilePassword']);
	Route::put('/profile/{id}/change-photo', [ProfileController::class, 'updateProfilePhoto']);
});
