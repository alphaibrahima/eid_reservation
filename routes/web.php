<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SlotController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Auth\PhoneVerificationController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;




// Routes authentifiées
Route::middleware(['auth'])->group(function () {
    // Réservations
    Route::get('/slots', [SlotController::class, 'index'])->name('slots.index');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

// Route publique pour les créneaux
Route::get('/slots/{date}', [SlotController::class, 'getAvailableSlots'])
->where('date', '[0-9]{4}-[0-9]{2}-[0-9]{2}')
->name('slots.byDate');
    
    // Vérification téléphone
    Route::get('/verify-phone', [PhoneVerificationController::class, 'show'])->name('verification.notice');
    Route::post('/verify-phone/send', [PhoneVerificationController::class, 'sendOTP'])->name('verification.send');
    Route::post('/verify-phone', [PhoneVerificationController::class, 'verify'])->name('verification.verify');
});

// Routes d'authentification
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

// Routes de base
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';