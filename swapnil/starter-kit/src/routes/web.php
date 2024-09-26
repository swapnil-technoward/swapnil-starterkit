<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Swapnil\StarterKit\Http\Controllers\Auth\LoginController;
use Swapnil\StarterKit\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('/');
Route::group(['middleware' => ['web']], function () {
    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/post-login', [LoginController::class, 'fakeLogin'])->name('fakeLogin');
    Route::get('/signup', [LoginController::class, 'showRegisterForm'])->name('signup');
    Route::post('/register', [LoginController::class, 'registerUser'])->name('register');
    Route::get('/forgot-password', [LoginController::class, 'forgotPassword'])->name('forgotPassword');
    Route::post('/reset-password', [LoginController::class, 'reset_password'])->name('reset_password');
    Route::get('/reset-hash', [LoginController::class, 'reset_hash'])->name('reset_hash');
    Route::group(['middleware' => ['starter.kit']], function () {
        Route::get('/dashboard', [HomeController::class,'index'])->name('dashboard');
        Route::get('/users', [HomeController::class,'users'])->name('users');
        Route::get('/delete/user/{id}', [HomeController::class,'delete_user'])->name('delete_user');
        Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    });
});
