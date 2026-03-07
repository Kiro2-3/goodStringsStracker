<?php

use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Transaction\TransactionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

Route::get('/', function () {
    // if the user is logged in send them directly to the dashboard
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// This route calls the "index" method in your TransactionController
Route::get('/dashboard', [TransactionController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Add Transaction page
    Route::get('/transactions/add', function () {
        return Inertia::render('AddTransaction');
    })->name('transactions.add');
    // Transaction routes: store (create), show (display edit form), update (save changes), and delete
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    // Show the edit form for a specific transaction
    Route::get('/transactions/{transaction}/edit', [TransactionController::class, 'show'])->name('transactions.edit');
    // Update the transaction with new data
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
    // Delete a transaction
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
