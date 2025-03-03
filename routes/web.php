<?php
use App\Http\Controllers\Auth\PhoneVerificationController;
use App\Http\Controllers\Auth\RegisteredUserController;


use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SlotController;

use App\Http\Controllers\ReservationController;





Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/slots', [SlotController::class, 'index'])->name('slots.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

Route::middleware(['auth'])->group(function () {
    Route::get('/verify-phone', [PhoneVerificationController::class, 'show'])->name('verification.notice');
    Route::post('/verify-phone/send', [PhoneVerificationController::class, 'sendOTP'])->name('verification.send');
    Route::post('/verify-phone', [PhoneVerificationController::class, 'verify'])->name('verification.verify');
});

require __DIR__.'/auth.php';
