<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockPriceController;

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


// Route::get('/stock-price/{symbol}', [StockPriceController::class, 'getRealTimePrice']);
Route::get('/stock-price/{symbol}', [StockPriceController::class, 'getRealTimePrice'])
    ->name('api.stock-price');
Route::get('/stock-symbols/{exchange?}', [StockPriceController::class, 'getSymbols']);





