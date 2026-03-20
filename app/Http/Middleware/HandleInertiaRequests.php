<?php

namespace App\Http\Middleware;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'recentTransactions' => fn () => $request->user()
                ? Transaction::query()
                    ->where('user_id', $request->user()->id)
                    ->latest('entry_date')
                    ->latest('id')
                    ->limit(5)
                    ->get(['id', 'description', 'amount', 'type', 'category', 'entry_date', 'created_at'])
                : [],
        ];
    }
}
