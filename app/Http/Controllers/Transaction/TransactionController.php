<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

/**
 * TransactionController handles all transaction-related operations
 * including listing, creating, editing, and deleting transactions.
 * This controller manages the income/expense tracking functionality.
 */
class TransactionController extends Controller
{
    // 1. Show the Dashboard with all transactions and summary
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Start building the query
        $query = $user->transactions();
        
        // Apply filters
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('date_from')) {
            $query->where('entry_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('entry_date', '<=', $request->date_to);
        }
        
        $transactions = $query->orderBy('entry_date', 'desc')->get();
        
        // For summary, we might want to show filtered or total summary
        // Let's show total summary always, but filtered transactions
        $totalIncome = $user->transactions()->where('type', 'income')->sum('amount');
        $totalExpense = $user->transactions()->where('type', 'expense')->sum('amount');

        // calculate totals per category for expenses and incomes separately
        $expenseData = $user->transactions()
            ->where('type', 'expense')
            ->selectRaw('category, sum(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $incomeData = $user->transactions()
            ->where('type', 'income')
            ->selectRaw('category, sum(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        // unified list of categories used by either type
        $categories = $expenseData->keys()->merge($incomeData->keys())->unique()->values();

        // arrays with totals aligned to categories index
        $expenseTotals = $categories->map(fn($c) => $expenseData->get($c, 0));
        $incomeTotals = $categories->map(fn($c) => $incomeData->get($c, 0));

        return Inertia::render('Dashboard', [
            'transactions' => $transactions,
            'summary' => [
                'income' => $totalIncome,
                'expense' => $totalExpense,
                'balance' => $totalIncome - $totalExpense
            ],
            'categories' => $categories,
            'expenseTotals' => $expenseTotals,
            'incomeTotals' => $incomeTotals,
            'filters' => $request->only(['category', 'type', 'date_from', 'date_to']),
        ]);
    }

    // 2. Save a new Transaction
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:income,expense',
            'category' => 'required|string',
            'entry_date' => 'required|date',
        ]);

        Auth::user()->transactions()->create($validated);

        return redirect()->back();
    }

    // 3. Show the edit form for a specific transaction
    // This displays the edit page where users can modify the transaction details
    public function show(Transaction $transaction)
    {
        // Authorization check: only the transaction owner can edit it
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        // Return the edit page with the transaction details pre-populated
        return Inertia::render('Transactions/Edit', [
            'transaction' => $transaction,
        ]);
    }

    // 4. Update a Transaction with new data
    // This method handles the PUT/PATCH request from the edit form
    public function update(Request $request, Transaction $transaction)
    {
        // Authorization check: only the transaction owner can update it
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        // Validate the incoming data from the edit form
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:income,expense',
            'category' => 'required|string',
            'entry_date' => 'required|date',
        ]);

        // Update the transaction with the new validated data
        $transaction->update($validated);

        // After successful update, redirect back to dashboard with a success message
        return redirect(route('dashboard'))->with('success', 'Transaction updated successfully');
    }

    // 5. Delete a Transaction
    // Only the transaction owner can delete it
    public function destroy(Transaction $transaction)
    {
        // Authorization check: only the transaction owner can delete it
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        // Remove the transaction from the database
        $transaction->delete();

        // Redirect back to where the user came from
        return redirect()->back();
    }
}
