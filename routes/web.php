<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/products', [App\Http\Controllers\productsController::class, 'index'])->name('products.index');
Route::post('/products', [App\Http\Controllers\productsController::class, 'store'])->name('products.store');
Route::get('/products', [App\Http\Controllers\productsController::class, 'create'])->name('products.create');
Route::get('/products', [App\Http\Controllers\productsController::class, 'show'])->name('products.show');
Route::put('/products', [App\Http\Controllers\productsController::class, 'update'])->name('products.update');
Route::delete('/products', [App\Http\Controllers\productsController::class, 'destroy'])->name('products.destroy');
Route::get('/products', [App\Http\Controllers\productsController::class, 'edit'])->name('products.edit');
