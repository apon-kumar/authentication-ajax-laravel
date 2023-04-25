<?php

use App\Http\Controllers\postController;
use App\Http\Controllers\userController;
use App\Http\Middleware\IsLoggedIn;
use Illuminate\Support\Facades\Route;

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


Route::get('/register', [userController::class, 'register']);
Route::get('/forgot-pass', [userController::class, 'forgotPass']);
Route::get('/reset-pass/{email}/{token}', [userController::class, 'resetPass'])->name('reset');

Route::post('/register', [userController::class, 'registerUser'])->name('auth.register');
Route::post('/login', [userController::class, 'loginUser'])->name('auth.login');
Route::post('/forgot-pass', [userController::class, 'forgotPassword'])->name('auth.forgot');
Route::post('/reset-password', [userController::class, 'resetPassword'])->name('auth.reset');

Route::group(['middleware' => ['IsLoggedIn']], function() {
    Route::get('/', [userController::class, 'login']);
    Route::get('/profile', [userController::class, 'profile'])->name('profile');
    Route::get('/logout', [userController::class, 'logout'])->name('auth.logout');
    Route::post('/profile-image', [userController::class, 'profileImageUpdate'])->name('profile.image');
    Route::post('/profile-update', [userController::class, 'profileUpdate'])->name('profile.update');


});