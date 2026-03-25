<?php

use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Transaction\TransactionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::get('/dashboard', [TransactionController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    // Bank Accounts page
    Route::get('/bank-accounts', [\App\Http\Controllers\BankAccount\BankAccountController::class, 'index'])
        ->name('bank-accounts.index');
    Route::post('/bank-accounts', [\App\Http\Controllers\BankAccount\BankAccountController::class, 'store'])
        ->name('bank-accounts.store');
    Route::put('/bank-accounts/{bankAccount}', [\App\Http\Controllers\BankAccount\BankAccountController::class, 'update'])
        ->name('bank-accounts.update');
    Route::delete('/bank-accounts/{bankAccount}', [\App\Http\Controllers\BankAccount\BankAccountController::class, 'destroy'])
        ->name('bank-accounts.destroy');
    Route::get('/transactions/recent', [TransactionController::class, 'recent'])
        ->name('transactions.recent');

    Route::get('/transactions/export-csv', [TransactionController::class, 'exportCsv'])
        ->name('transactions.export-csv');

    Route::get('/transactions/add', [TransactionController::class, 'create'])
        ->name('transactions.add');

    Route::post('/transactions', [TransactionController::class, 'store'])
        ->name('transactions.store');
    Route::post('/transactions/bulk-delete', [TransactionController::class, 'bulkDestroy'])
        ->name('transactions.bulk-delete');

    Route::get('/transactions/{transaction}/edit', [TransactionController::class, 'show'])
        ->name('transactions.edit');

    Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])
        ->name('transactions.update');

    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])
        ->name('transactions.destroy');

    Route::get('/categories', [CategoryController::class, 'index'])
        ->name('categories.index');

    Route::post('/categories', [CategoryController::class, 'store'])
        ->name('categories.store');

    Route::put('/categories/{category}', [CategoryController::class, 'update'])
        ->name('categories.update');

    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
        ->name('categories.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // AI chat endpoint (auth required). If OPENAI_API_KEY is set, requests will be proxied to OpenAI.
    Route::post('/ai-chat', [\App\Http\Controllers\AiChatController::class, 'chat'])
        ->name('ai.chat');
});

require __DIR__.'/auth.php';
