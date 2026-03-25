<?php

namespace App\Http\Controllers\BankAccount;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\BankAccount;


class BankAccountController
{

    /**
     * Store a new bank account for the user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'balance' => 'nullable|numeric',
        ]);

        $user = $request->user();
        // ensure balance exists
        if (!isset($validated['balance'])) {
            $validated['balance'] = 0;
        }

        $user->bankAccounts()->create($validated);

        return redirect()->route('bank-accounts.index')->with('success', 'Bank account added successfully.');
    }
    /**
     * Show the Bank Accounts page.
     */
    public function index(Request $request): Response
    {
        $user = Auth::user();
        // paginate bank accounts for the user (3 per page)
        $bankAccounts = $user->bankAccounts()->latest()->paginate(3);
        $totalBalance = (float) $user->bankAccounts()->sum('balance');

        return Inertia::render('BankAccounts', [
            'auth' => ['user' => $user],
            'bankAccounts' => $bankAccounts,
            'totalBalance' => $totalBalance,
        ]);
    }

    /**
     * Update a bank account for the authenticated user.
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        $user = $request->user();

        if ($bankAccount->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'balance' => 'nullable|numeric',
        ]);

        $bankAccount->update($validated);

        return redirect()->route('bank-accounts.index');
    }

    /**
     * Delete a bank account for the authenticated user.
     */
    public function destroy(Request $request, BankAccount $bankAccount)
    {
        $user = $request->user();

        if ($bankAccount->user_id !== $user->id) {
            abort(403);
        }

        $bankAccount->delete();

        return redirect()->route('bank-accounts.index')->with('success', 'Bank account deleted successfully.');
    }
}
