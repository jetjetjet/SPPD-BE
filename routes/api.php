<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnggaranController;
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
	Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

	Route::get('/anggaran-grid', [AnggaranController::class, 'grid']);
	Route::resource('anggaran', AnggaranController::class)->only([
    'show', 'store', 'update', 'destroy'
	]);

	Route::get('/role/{id}', [AnggaranController::class, 'show']);
	Route::post('/role', [AnggaranController::class, 'store']);
	Route::put('/role/{id}', [AnggaranController::class, 'update']);
	Route::delete('/role/{id}',  [AnggaranController::class, 'destroy']);
	
	Route::get('/role-grid', [RoleController::class, 'grid']);
	Route::get('/role-permissions', [RoleController::class, 'getPermission']);
	Route::resource('role', RoleController::class)->only([
    'show', 'store', 'update', 'destroy'
	]);
	
	Route::get('/user-grid', [UserController::class, 'grid']);
	Route::resource('user', RoleController::class)->only([
    'show', 'store', 'update', 'destroy'
	]);
	Route::put('/user/{id}/change-password', [UserController::class, 'changePassword']);
	Route::put('/user/{id}/change-photo', [UserController::class, 'changePhoto']);

	Route::resource('profile', RoleController::class)->only([
    'show', 'update'
	]);
	Route::put('/profile/{id}/change-password', [ProfileController::class, 'updateProfilePassword']);
	Route::put('/profile/{id}/change-photo', [ProfileController::class, 'updateProfilePhoto']);
});
