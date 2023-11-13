<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\ClientController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ItemController;
use App\Http\Controllers\API\StockController;

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



Route::middleware('tokenValidation')->group(function () {
    Route::get('client', [ClientController::class, 'show'])->name('client.show');

    Route::get('product', [ProductController::class, 'index'])->name('product.index');
    Route::post('product', [ProductController::class, 'store'])->name('product.store');
    Route::get('product/{id}', [ProductController::class, 'show'])->name('product.show');
    Route::put('product/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::delete('product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');

    Route::get('item/{product_id}', [ItemController::class, 'index'])->name('item.index');
    Route::post('item', [ItemController::class, 'store'])->name('item.store');
    Route::get('item/unique/{identifier}', [ItemController::class, 'show'])->name('item.unique.show');
    Route::put('item/{identifier}', [ItemController::class, 'update'])->name('item.update');
    Route::delete('item/{identifier}', [ItemController::class, 'destroy'])->name('item.destroy');

    Route::get('stock', [StockController::class, 'index'])->name('stock.index');
    Route::put('stock/{stockId}/quantity', [StockController::class, 'update'])->name('stock.update');
});
