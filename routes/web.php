<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Users\UsersController;
use App\Http\Controllers\Menu\MenuController;
use App\Http\Controllers\Pemesanan\PemesananController;
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

Route::get('/users', [UsersController::class, 'index'])->name('users');
Route::get('/users/data', [UsersController::class, 'getData'])->name('users.data');
Route::delete('/users/{id}', [UsersController::class, 'destroy'])->name('users.destroy');

Route::get('/menu', [MenuController::class, 'index'])->name('index.menu');
Route::get('/menu/create', [MenuController::class, 'create'])->name('create.menu');
Route::post('/menu/store', [MenuController::class, 'store'])->name('store.menu');
Route::get('/menu/{id}', [MenuController::class, 'show'])->name('show.menu');
Route::get('/menu/edit/{id}', [MenuController::class, 'edit'])->name('edit.menu');
Route::put('/menu/update/{id}', [MenuController::class, 'update'])->name('update.menu');
Route::delete('/menu/delete/{id}', [MenuController::class, 'destroy'])->name('destroy.menu');


// // Rute registrasi untuk guest (tanpa login)
// Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
// Route::post('/register', [RegisterController::class, 'register']);

// // Rute registrasi untuk admin (dengan login)
// Route::middleware(['auth'])->group(function () {
//     Route::get('/admin/register', [RegisterController::class, 'showRegistrationForm'])->name('admin.register');
//     Route::post('/admin/register', [RegisterController::class, 'register']);
// });
