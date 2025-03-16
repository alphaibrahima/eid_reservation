<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;

// Routes publiques
Route::get('/', function () {
    return view('welcome');
});

// Authentification
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

// Routes authentifiées
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profil utilisateur
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Réservations
    Route::prefix('reservation')->group(function () {
        // Processus de réservation
        Route::get('/', [ReservationController::class, 'index'])->name('reservation.index');
        Route::get('/etape-1', [ReservationController::class, 'step1'])->name('reservation.step1');
        Route::post('/select-day', [ReservationController::class, 'selectDay'])->name('reservation.select-day');
        Route::get('/etape-2', [ReservationController::class, 'step2'])->name('reservation.step2');
        Route::post('/select-slot', [ReservationController::class, 'selectSlot'])->name('reservation.select-slot');
        Route::get('/etape-3', [ReservationController::class, 'step3'])->name('reservation.step3');
        Route::post('/confirm', [ReservationController::class, 'confirm'])->name('reservation.confirm');
        Route::get('/etape-4', [ReservationController::class, 'processPayment'])->name('reservation.step4');
        
        // PDF et retour paiement
        Route::get('/pdf/{code}', [ReservationController::class, 'generatePdf'])->name('reservation.pdf');
    });

    // Paiement Stripe
    Route::get('/payment-return', function() {
        return redirect()->route('reservation.index')->with('status', 'Paiement confirmé !');
    })->name('payment.return');
});

require __DIR__.'/auth.php';