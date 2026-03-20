<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    /**
     * Display the dashboard with transaction summary and charts.
     *
     * @param  Request $request the incoming HTTP request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $user = Auth::user();

        $transactions = $user->transactions()
            ->orderBy('entry_date', 'desc')
            ->paginate(10);

        $chartFilters = [
            'category'  => $request->input('chart_category'),
            'type'      => $request->input('chart_type'),
            'date_from' => $request->input('chart_date_from'),
            'date_to'   => $request->input('chart_date_to'),
        ];

        $chartQuery = $this->applyChartFilters($user->transactions(), $chartFilters);

        $totalIncome  = (clone $chartQuery)->where('type', 'income')->sum('amount');
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

        $categories    = $expenseData->keys()->merge($incomeData->keys())->unique()->values();
        $expenseTotals = $categories->map(fn ($c) => $expenseData->get($c, 0));
        $incomeTotals  = $categories->map(fn ($c) => $incomeData->get($c, 0));

        $chartTransactions = (clone $chartQuery)
            ->orderBy('entry_date')
            ->get(['entry_date', 'type', 'amount', 'category']);

        $allCategories = $user->categories()->orderBy('name')->pluck('name');

        if ($allCategories->isEmpty()) {
            $allCategories = $user->transactions()
                ->select('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category');
        }

        $transactionCategories = $user->transactions()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return Inertia::render('Dashboard', [
            'auth'                  => ['user' => $user],
            'transactions'          => $transactions,
            'summary'               => [
                'income'  => number_format($totalIncome, 2, '.', ''),
                'expense' => number_format($totalExpense, 2, '.', ''),
                'balance' => number_format($totalIncome - $totalExpense, 2, '.', ''),
            ],
            'categories'            => $allCategories,
            'transactionCategories' => $transactionCategories,
            'expenseTotals'         => $expenseTotals,
            'incomeTotals'          => $incomeTotals,
            'chartTransactions'     => $chartTransactions,
            'filters'               => $request->only(['category', 'type', 'date_from', 'date_to']),
            'chartFilters'          => $chartFilters,
        ]);
    }

    /**
     * Display the add transaction page.
     *
     * @return Response
     */
    public function create(): Response
    {
        $user = Auth::user();

        $categories = $user->categories()->orderBy('name')->pluck('name');

        if ($categories->isEmpty()) {
            $categories = $user->transactions()
                ->select('category')
                ->distinct()
                ->pluck('category');
        }

        return Inertia::render('AddTransaction', [
            'categories' => $categories,
            'standalone' => true,
        ]);
    }

    /**
     * Display the recent transactions page with filters.
     *
     * @param  Request $request the incoming HTTP request
     * @return Response
     */
    public function recent(Request $request): Response
    {
        $user  = Auth::user();
        $query = $this->applyTransactionFilters($user->transactions()->orderBy('entry_date', 'desc'), $request);

        $transactions = $query->paginate(10)->withQueryString();

        $categories = $user->categories()->orderBy('name')->pluck('name');

        if ($categories->isEmpty()) {
            $categories = $user->transactions()->select('category')->distinct()->pluck('category');
        }

        return Inertia::render('RecentTransactions', [
            'auth'         => ['user' => $user],
            'transactions' => $transactions,
            'filters'      => $request->only(['search', 'type', 'category', 'date_from', 'date_to']),
            'categories'   => $categories,
        ]);
    }

    /**
     * Store a newly created transaction.
     *
     * @param  StoreTransactionRequest $request the validated request data
     * @return RedirectResponse
     */
    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $user      = Auth::user();
        $validated = $request->validated();

        $user->transactions()->create($validated);

        Category::firstOrCreate([
            'user_id' => $user->id,
            'name'    => $validated['category'],
        ]);

        return redirect()->back(302, [], route('dashboard'))->with('success', 'Successfully added transaction');
    }

    /**
     * Update an existing transaction.
     *
     * @param  UpdateTransactionRequest $request     the validated request data
     * @param  Transaction              $transaction the transaction to update
     * @return RedirectResponse
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validated();

        $transaction->update($validated);

        Category::firstOrCreate([
            'user_id' => $transaction->user_id,
            'name'    => $validated['category'],
        ]);

        return redirect()->back(302, [], route('dashboard'))->with('success', 'Transaction updated successfully');
    }

    /**
     * Delete an existing transaction.
     *
     * @param  Transaction $transaction the transaction to delete
     * @return RedirectResponse
     */
    public function destroy(Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $transaction->delete();

        return redirect()->back(302, [], route('dashboard'))->with('success', 'Successfully deleted transaction');
    }

    /**
     * Export transactions as a CSV file, respecting the same filters as recent().
     *
     * @param  Request $request the incoming HTTP request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user  = Auth::user();
        $query = $this->applyTransactionFilters($user->transactions()->orderBy('entry_date', 'desc'), $request);

        $transactions = $query->get(['entry_date', 'description', 'category', 'amount', 'type']);

        $filename = 'transactions_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($transactions): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Description', 'Category', 'Amount', 'Type']);

            foreach ($transactions as $t) {
                fputcsv($handle, [
                    $t->entry_date,
                    $t->description,
                    $t->category,
                    $t->amount,
                    $t->type,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Apply search and field filters to a transaction query.
     *
     * @param  Builder $query   the base Eloquent query builder
     * @param  Request $request the incoming HTTP request containing filter params
     * @return Builder
     */
    private function applyTransactionFilters(Builder $query, Request $request): Builder
    {
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

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function (Builder $q) use ($search): void {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");

                if (is_numeric($search)) {
                    $q->orWhere('amount', (float) $search);
                }
            });
        }

        return $query;
    }

    /**
     * Apply chart-specific filters to a transaction query.
     *
     * @param  Builder              $query   the base Eloquent query builder
     * @param  array<string, mixed> $filters key-value pairs of chart filter values
     * @return Builder
     */
    private function applyChartFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('entry_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('entry_date', '<=', $filters['date_to']);
        }

        return $query;
    }
}
