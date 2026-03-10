<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    // 5. Show recent transactions with filters
    public function recent(Request $request)
    {
        $user = Auth::user();
        $query = $user->transactions()->orderBy('entry_date', 'desc');

        // Apply filters if present
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }
        if ($request->filled('date_from')) {
            $query->where('entry_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->where('entry_date', '<=', $request->input('date_to'));
        }

        $transactions = $query->paginate(10)->withQueryString();

        // Get all categories for filter dropdown
        $categories = $user->transactions()->select('category')->distinct()->pluck('category');

        return Inertia::render('RecentTransactions', [
            'auth' => ['user' => $user],
            'transactions' => $transactions,
            'filters' => $request->only(['type', 'category', 'date_from', 'date_to']),
            'categories' => $categories,
        ]);
    }
    // 1. Show the Dashboard with all transactions and summary
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Dashboard: Only chart filters affect summary and charts, not the transaction table
        // 1. Get all transactions for the table (no filters except pagination)
        $transactions = $user->transactions()->orderBy('entry_date', 'desc')->paginate(10);

        // 2. Apply chart filters for summary and charts
        $chartFilters = [
            'category' => $request->input('chart_category'),
            'type' => $request->input('chart_type'),
            'date_from' => $request->input('chart_date_from'),
            'date_to' => $request->input('chart_date_to'),
        ];
        $chartQuery = $user->transactions();
        if ($chartFilters['category']) {
            $chartQuery->where('category', $chartFilters['category']);
        }
        if ($chartFilters['type']) {
            $chartQuery->where('type', $chartFilters['type']);
        }
        if ($chartFilters['date_from']) {
            $chartQuery->where('entry_date', '>=', $chartFilters['date_from']);
        }
        if ($chartFilters['date_to']) {
            $chartQuery->where('entry_date', '<=', $chartFilters['date_to']);
        }

        $totalIncome = (clone $chartQuery)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $chartQuery)->where('type', 'expense')->sum('amount');

        $expenseData = (clone $chartQuery)
            ->where('type', 'expense')
            ->selectRaw('category, sum(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $incomeData = (clone $chartQuery)
            ->where('type', 'income')
            ->selectRaw('category, sum(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $categories = $expenseData->keys()->merge($incomeData->keys())->unique()->values();
        $expenseTotals = $categories->map(fn($c) => $expenseData->get($c, 0));
        $incomeTotals = $categories->map(fn($c) => $incomeData->get($c, 0));

        return Inertia::render('Dashboard', [
            'auth' => ['user' => $user],
            'transactions' => $transactions,
            'summary' => [
                'income' => number_format($totalIncome, 2, '.', ''),
                'expense' => number_format($totalExpense, 2, '.', ''),
                'balance' => number_format($totalIncome - $totalExpense, 2, '.', '')
            ],
            'categories' => $categories,
            'expenseTotals' => $expenseTotals,
            'incomeTotals' => $incomeTotals,
            'filters' => $request->only(['category', 'type', 'date_from', 'date_to']),
            'chartFilters' => $chartFilters,
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

        return redirect()->route('dashboard')->with('success', 'Successfully added transaction');
    }

    // 3. Update a Transaction
    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:income,expense',
            'category' => 'required|string',
            'entry_date' => 'required|date',
        ]);

        $transaction->update($validated);

        return redirect()->back()->with('success', 'Transaction updated successfully');
    }

    // 4. Delete a Transaction
    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $transaction->delete();

        return redirect()->back()->with('success', 'Successfully deleted transaction');
    }
}