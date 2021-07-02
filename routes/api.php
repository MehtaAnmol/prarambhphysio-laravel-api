<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
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

Route::post('/register',[AuthController::class, 'register']);
Route::post('/login',[AuthController::class, 'login']);
Route::get('/recent-posts', [PostController::class, 'showRecentPosts']);
Route::get('/posts/{id}',[PostController::class, 'show']);
Route::group(['middleware' => ['auth:sanctum']], function () {
    /* Logout */
    
    Route::post('/logout',[AuthController::class, 'logout']);

    /* ./Logout */

    /* Posts */
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts',[PostController::class, 'store']);
    Route::put('/posts/{id}',[PostController::class, 'update']);
    Route::delete('/posts/{id}',[PostController::class, 'destroy']);

    /* ./Posts */

    /* Users */
    
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}',[UserController::class, 'show']);

    /* ./Users */

    /* Images */

    Route::get('/images', [ImageController::class, 'index']);
    Route::get('/images/{id}', [ImageController::class, 'show']);
    Route::post('/images', [ImageController::class, 'store']);
    Route::put('/images/{id}', [ImageController::class, 'update']);
    Route::delete('/images/{id}', [ImageController::class, 'destroy']);

    /* Images */
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});