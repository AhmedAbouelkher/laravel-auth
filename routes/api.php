<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;

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

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        // Logout
        Route::post('/logout', [AuthController::class, 'logout']);
        // about
        Route::get('/about', [AuthController::class, 'about']);
    });
});

Route::prefix('account')->middleware('auth:sanctum')->group(function () {
    Route::get('/products', [ProductController::class, 'userProducts']);

    // Create product
    Route::post('/products', [ProductController::class, 'store']);

    // Update product with id
    Route::put('/products/{id}', [ProductController::class, 'update']);

    // Delete product with id
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});

Route::prefix('products')->group(function () {
    // show all products
    Route::get('/', [ProductController::class, 'index']);

    // Show product with id
    Route::get('/{id}', [ProductController::class, 'show']);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
