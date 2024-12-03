<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Menu\MenuController;
use App\Http\Controllers\Pemesanan\PemesananController;

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

Route::get('/', [PemesananController::class, 'index'])->name('index.pemesanan');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/users', [App\Http\Controllers\Users\UsersController::class, 'index'])->name('users');
Route::delete('/users/{id}', [App\Http\Controllers\Users\UsersController::class, 'destroy'])->name('users.destroy');

Route::get('/menu', [MenuController::class, 'index'])->name('index.menu');
Route::get('/menu/create', [MenuController::class, 'create'])->name('create.menu');
Route::post('/menu/store', [MenuController::class, 'store'])->name('store.menu');
Route::get('/menu/{id}', [MenuController::class, 'show'])->name('show.menu');
Route::get('/menu/edit/{id}', [MenuController::class, 'edit'])->name('edit.menu');
Route::put('/menu/update/{id}', [MenuController::class, 'update'])->name('update.menu');
Route::delete('/menu/delete/{id}', [MenuController::class, 'destroy'])->name('destroy.menu');
