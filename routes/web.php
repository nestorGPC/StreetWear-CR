<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ReportController;


/*
|--------------------------------------------------------------------------
| Inicio
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('products.index');
});


/*
|--------------------------------------------------------------------------
| Productos
|--------------------------------------------------------------------------
*/

Route::get('/productos', [ProductController::class, 'index'])
    ->name('products.index');

Route::get('/productos/{product}', [ProductController::class, 'show'])
    ->name('products.show');


/*
|--------------------------------------------------------------------------
| Carrito
|--------------------------------------------------------------------------
*/

Route::get('/carrito', [CartController::class, 'index'])
    ->name('cart.index');

Route::post('/carrito/agregar/{product}', [CartController::class, 'add'])
    ->name('cart.add');

Route::put('/carrito/actualizar/{product}', [CartController::class, 'update'])
    ->name('cart.update');

Route::delete('/carrito/eliminar/{product}', [CartController::class, 'remove'])
    ->name('cart.remove');


/*
|--------------------------------------------------------------------------
| Invitados
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/registro', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/registro', [AuthController::class, 'register'])
        ->name('register.store');

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store')
        ->middleware('throttle:5,1');
});


/*
|--------------------------------------------------------------------------
| Clientes autenticados
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/mi-cuenta', [AccountController::class, 'dashboard'])
        ->name('account.dashboard');

    Route::get('/mi-cuenta/perfil', [AccountController::class, 'editProfile'])
        ->name('account.profile');

    Route::put('/mi-cuenta/perfil', [AccountController::class, 'updateProfile'])
        ->name('account.profile.update');

    Route::get('/mi-cuenta/pedidos', [AccountController::class, 'orders'])
        ->name('account.orders');

    Route::get(
        '/mi-cuenta/pedidos/{order}',
        [AccountController::class, 'showOrder']
    )->name('account.orders.show');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/checkout', [CheckoutController::class, 'index'])
        ->name('checkout.index');

    Route::post('/checkout', [CheckoutController::class, 'store'])
        ->name('checkout.store');

    Route::get(
        '/checkout/confirmacion/{order}',
        [CheckoutController::class, 'success']
    )->name('checkout.success');


    /*
    |--------------------------------------------------------------------------
    | Reportes
    |--------------------------------------------------------------------------
    */

    Route::get('/reportes', [ReportController::class, 'index'])
        ->name('reports.index');

    Route::get('/reportes/pedidos', [ReportController::class, 'orders'])
        ->name('reports.orders');

    Route::get('/reportes/ventas', [ReportController::class, 'sales'])
        ->name('reports.sales');

    Route::get('/reportes/productos', [ReportController::class, 'products'])
        ->name('reports.products');
});
