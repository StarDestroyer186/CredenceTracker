<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\MemcachedController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [LoginController::class,'index'])->name('login');

Route::middleware('auth:api')->group(function () {
    Route::get('/logout', [LoginController::class,'logout']);
    Route::get('/findUser/{USER_ID}', [LoginController::class,'findUser']);
});

Route::get('/memcached', [MemcachedController::class, 'index']);




