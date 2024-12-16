<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Users\UsersController;
use App\Http\Controllers\Menu\MenuController;
use App\Http\Controllers\Pemesanan\PemesananController;
use App\Http\Controllers\Transaksi\TransaksiController;
use App\Http\Controllers\Auth\RegisterController;

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
Route::post('/pilih-metode', [PemesananController::class, 'pilihMetode'])->name('pembayaran.pilih');
Route::post('/proses-pembayaran', [PemesananController::class, 'prosesPembayaran'])->name('pembayaran.proses');

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/users', [UsersController::class, 'index'])->name('users.index');
Route::get('/users/data', [UsersController::class, 'getData'])->name('users.data');
Route::get('/users/edit/{id}', [UsersController::class, 'edit'])->name('users.edit');
Route::put('/users/update/{id}', [UsersController::class, 'update'])->name('users.update');
Route::delete('/users/delete/{id}', [UsersController::class, 'destroy'])->name('users.destroy');

Route::get('/menu', [MenuController::class, 'index'])->name('index.menu');
Route::get('/menu/data', [MenuController::class, 'getData'])->name('menu.data');
Route::get('/menu/create', [MenuController::class, 'create'])->name('create.menu');
Route::post('/menu/store', [MenuController::class, 'store'])->name('store.menu');
Route::get('/menu/{id}', [MenuController::class, 'show'])->name('show.menu');
Route::get('/menu/edit/{id}', [MenuController::class, 'edit'])->name('edit.menu');
Route::put('/menu/update/{id}', [MenuController::class, 'update'])->name('update.menu');
Route::delete('/menu/delete/{id}', [MenuController::class, 'destroy'])->name('destroy.menu');

Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
Route::get('/transaksi/data', [TransaksiController::class, 'getData'])->name('transaksi.data'); // Untuk DataTables
Route::get('/transaksi/{id}', [TransaksiController::class, 'show'])->name('transaksi.show');
