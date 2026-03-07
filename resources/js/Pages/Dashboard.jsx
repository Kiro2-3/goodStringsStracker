import { Tooltip, LineChart, Line, XAxis, YAxis, Legend, CartesianGrid, ResponsiveContainer, PieChart, Pie, Cell, BarChart, Bar } from 'recharts';
import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, router, Link } from '@inertiajs/react';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';

// ...existing code...

// color palettes
const incomeColor = '#22d399';
const expenseColor = '#ef4444';
const pieColors = ['#6366f1', '#ec4899', '#22d3ee', '#facc15', '#34d399', '#f87171'];

export default function Dashboard({ auth, transactions, summary, categories, expenseTotals, incomeTotals, filters }) {
        // Pagination state for transactions
        const [currentPage, setCurrentPage] = React.useState(1);
        const itemsPerPage = 10;
        const totalPages = Math.ceil(transactions.length / itemsPerPage);
        const paginatedTransactions = transactions.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);
    const [tab, setTab] = React.useState('dashboard');
    // ...existing code...
    const { data, setData, post, processing, reset, errors } = useForm({
        description: '',
        amount: '',
        type: 'expense',
        category: 'Food',
        entry_date: new Date().toISOString().split('T')[0],
    });
    // When type changes, set category to 'Salary' for income, else to first expense category
    React.useEffect(() => {
        if (data.type === 'income') {
            setData('category', 'Salary');
        } else if (data.type === 'expense' && data.category === 'Salary') {
            setData('category', 'Food');
        }
    }, [data.type]);
    const submit = (e) => {
        e.preventDefault();
        post(route('transactions.store'), {
            onSuccess: () => reset(),
        });
    };
    const barChartData = categories.map((cat, idx) => ({
        category: cat,
        income: incomeTotals[idx] || 0,
        expense: expenseTotals[idx] || 0,
    }));
    const expenseByCategory = {};
    transactions.forEach((t) => {
        if (t.type === 'expense') {
            if (!expenseByCategory[t.category]) {
                expenseByCategory[t.category] = 0;
            }
            expenseByCategory[t.category] += Number(t.amount);
        }
    });
    const pieChartData = Object.entries(expenseByCategory).map(([name, value]) => ({ name, value }));

        // Prepare line chart data: group by date, sum income and expense per date
        const dateMap = {};
        transactions.forEach((t) => {
            const date = t.entry_date;
            if (!dateMap[date]) {
                dateMap[date] = { date, income: 0, expense: 0 };
            }
            if (t.type === 'income') {
                dateMap[date].income += parseFloat(t.amount);
            } else {
                dateMap[date].expense += parseFloat(t.amount);
            }
        });
        // Sort by date ascending
        const lineData = Object.values(dateMap).sort((a, b) => a.date.localeCompare(b.date));
    // Logout handler to redirect to login after logout
    const handleLogout = (e) => {
        e.preventDefault();
        router.post(route('logout'), {}, {
            onSuccess: () => {
                router.visit(route('login'));
            },
        });
    };

    return (
        <>
            <Head title="Stracker" />
            <div className="min-h-screen w-full flex bg-gray-50 text-gray-900">
                {/* Sidebar menu */}
                <div className="w-56 flex flex-col gap-4 items-stretch min-h-[32rem] justify-start bg-white border-r border-gray-200 p-4">
                    <button
                        className={`btn btn-block ${tab === 'dashboard' ? 'btn-primary' : 'btn-ghost'}`}
                        onClick={() => setTab('dashboard')}
                    >
                        Dashboard
                    </button>
                    <button
                        className={`btn btn-block ${tab === 'transactions' ? 'btn-primary' : 'btn-ghost'}`}
                        onClick={() => setTab('transactions')}
                    >
                        Recent Transactions
                    </button>
                    <Link
                        href={route('transactions.add')}
                        className="btn btn-block bg-gray-200 hover:bg-gray-300 text-gray-800 border-none shadow-none"
                    >
                        Add Transaction
                    </Link>
                    <form onSubmit={handleLogout} className="mt-auto">
                        <button
                            type="submit"
                            className="btn btn-block btn-error font-bold text-white hover:bg-red-700 transition-colors duration-150"
                        >
                            Logout
                        </button>
                    </form>
                </div>
                {/* Main content */}
                <div className="flex-1 space-y-8 px-8 py-8">
                    <h1 className="text-4xl font-extrabold text-left text-indigo-700 mb-8">Stracker</h1>
                    {tab === 'dashboard' && (
                        <>
                            {/* 1. Summary Cards using DaisyUI stats */}
                            <div className="stats shadow w-full bg-white">
                                <div className="stat">
                                    <div className="stat-title text-gray-700">Total Income</div>
                                    <div className="stat-value text-green-600">₱{summary.income}</div>
                                </div>
                                <div className="stat">
                                    <div className="stat-title text-gray-700">Total Expenses</div>
                                    <div className="stat-value text-red-600">₱{summary.expense}</div>
                                </div>
                                <div className="stat">
                                    <div className="stat-title text-gray-700">Total Revenue</div>
                                    <div className="stat-value text-blue-600">₱{summary.balance}</div>
                                </div>
                            </div>
                            {/* Line chart for total income vs total expense */}
                            <div className="card bg-white shadow mb-6 w-full">
                                <div className="card-body">
                                    <h3 className="font-bold mb-4 text-lg text-blue-600">Income vs Expense Over Time</h3>
                                    <div style={{ width: '100%', height: 300 }}>
                                        <ResponsiveContainer>
                                            <LineChart data={lineData} margin={{ top: 10, right: 30, left: 0, bottom: 0 }}>
                                                <CartesianGrid strokeDasharray="3 3" />
                                                <XAxis dataKey="date" />
                                                <YAxis />
                                                <Tooltip formatter={(value) => `₱${Number(value).toLocaleString()}`} />
                                                <Legend />
                                                <Line type="monotone" dataKey="income" stroke="#22c55e" name="Income" dot={false} />
                                                <Line type="monotone" dataKey="expense" stroke="#ef4444" name="Expense" dot={false} />
                                            </LineChart>
                                        </ResponsiveContainer>
                                    </div>
                                </div>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 w-full">
                                {/* 3. Category Breakdown Diagram (Pie Chart) */}
                                <div className="card bg-white shadow md:col-span-2 h-96 p-6 flex flex-col items-center justify-center w-full">
                                    <h3 className="font-bold mb-4 text-lg text-center text-purple-600">Category Breakdown</h3>
                                    {(pieChartData.length > 0) ? (
                                        <div style={{ width: '100%', height: 320 }}>
                                            <ResponsiveContainer width="100%" height="100%">
                                                <PieChart>
                                                    <Pie
                                                        data={pieChartData}
                                                        dataKey="value"
                                                        nameKey="name"
                                                        cx="50%"
                                                        cy="50%"
                                                        outerRadius={110}
                                                        fill="#6366f1"
                                                        label={({ name, value }) => `${name}: ₱${value}`}
                                                    >
                                                        {pieChartData.map((entry, idx) => (
                                                            <Cell key={`cell-${idx}`} fill={pieColors[idx % pieColors.length]} />
                                                        ))}
                                                    </Pie>
                                                    <Tooltip />
                                                    <Legend verticalAlign="bottom" />
                                                </PieChart>
                                            </ResponsiveContainer>
                                        </div>
                                    ) : (
                                        <p className="text-gray-500 mt-10">Add transactions to see the diagram</p>
                                    )}
                                </div>
                            </div>
                        </>
                    )}
                    {tab === 'transactions' && (
                        <div className="card bg-white shadow overflow-x-auto w-full">
                            <div className="card-body">
                                <h3 className="font-bold mb-4 text-lg text-indigo-600">Recent Transactions</h3>
                                {/* Filter Form */}
                                <div className="mb-4 p-4 bg-gray-100 rounded-lg">
                                    <h4 className="font-semibold mb-2 text-gray-700">Filter Transactions</h4>
                                    <form className="grid grid-cols-1 md:grid-cols-4 gap-4" onSubmit={(e) => {
                                        e.preventDefault();
                                        const formData = new FormData(e.target);
                                        const params = new URLSearchParams();
                                        for (let [key, value] of formData.entries()) {
                                            if (value) params.append(key, value);
                                        }
                                        router.get(route('dashboard'), Object.fromEntries(params));
                                    }}>
                                        <div className="form-control">
                                            <InputLabel value="Category" htmlFor="category_filter" />
                                            <select
                                                id="category_filter"
                                                name="category"
                                                className="select select-bordered w-full text-black bg-white"
                                                defaultValue={filters?.category || ''}
                                            >
                                                <option value="">All Categories</option>
                                                <option value="Food">Food</option>
                                                <option value="Salary">Salary</option>
                                                <option value="Rent">Rent</option>
                                                <option value="Leisure">Leisure</option>
                                                <option value="Bills">Bills</option>
                                                <option value="Transportation">Transportation</option>
                                                <option value="Utilities">Utilities</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div className="form-control">
                                            <InputLabel value="Type" htmlFor="type_filter" />
                                            <select
                                                id="type_filter"
                                                name="type"
                                                className="select select-bordered w-full text-black bg-white"
                                                defaultValue={filters?.type || ''}
                                            >
                                                <option value="">All Types</option>
                                                <option value="income">Income</option>
                                                <option value="expense">Expense</option>
                                            </select>
                                        </div>
                                        <div className="form-control">
                                            <InputLabel value="From Date" htmlFor="date_from" />
                                            <input
                                                id="date_from"
                                                name="date_from"
                                                type="date"
                                                className="input input-bordered w-full text-black bg-white"
                                                defaultValue={filters?.date_from || ''}
                                            />
                                        </div>
                                        <div className="form-control">
                                            <InputLabel value="To Date" htmlFor="date_to" />
                                            <input
                                                id="date_to"
                                                name="date_to"
                                                type="date"
                                                className="input input-bordered w-full text-black bg-white"
                                                defaultValue={filters?.date_to || ''}
                                            />
                                        </div>
                                        <div className="form-control md:col-span-4 flex gap-2">
                                            <PrimaryButton type="submit" className="btn-sm">
                                                Apply Filters
                                            </PrimaryButton>
                                            <button
                                                type="button"
                                                className="btn btn-ghost btn-sm"
                                                onClick={() => router.get(route('dashboard'))}
                                            >
                                                Clear Filters
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <table className="table table-zebra w-full">
                                    <thead>
                                        <tr>
                                            <th className="text-gray-700 font-bold">Date</th>
                                            <th className="text-gray-700 font-bold">Description</th>
                                            <th className="text-gray-700 font-bold">Category</th>
                                            <th className="text-right text-gray-700 font-bold">Amount</th>
                                            <th className="text-center text-gray-700 font-bold">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {paginatedTransactions.length > 0 ? paginatedTransactions.map((t) => (
                                            <tr key={t.id}>
                                                <td className="text-gray-800">{t.entry_date}</td>
                                                <td>
                                                    <div className="font-medium text-gray-800">{t.description}</div>
                                                    <div className="text-xs uppercase font-semibold text-gray-500">{t.type}</div>
                                                </td>
                                                <td>
                                                    <span className="badge badge-outline text-gray-800">
                                                        {t.category}
                                                    </span>
                                                </td>
                                                <td className="text-right font-bold" style={{color: t.type === 'income' ? '#16a34a' : '#dc2626'}}>
                                                    {t.type === 'income' ? '+' : '-'}₱{t.amount}
                                                </td>
                                                <td className="text-center">
                                                    <div className="flex gap-2 justify-center">
                                                        <Link
                                                            href={route('transactions.edit', t.id)}
                                                            className="btn btn-ghost btn-sm btn-square"
                                                            title="Edit Transaction"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </Link>
                                                        <button 
                                                            onClick={() => {
                                                                if(confirm('Are you sure you want to delete this transaction?')) {
                                                                    router.delete(route('transactions.destroy', t.id))
                                                                }
                                                            }}
                                                            className="btn btn-ghost btn-sm btn-square text-error"
                                                            title="Delete Transaction"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        )) : (
                                            <tr>
                                                <td colSpan="5" className="p-10 text-center text-gray-500">
                                                    No transactions found yet.
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                                {/* Pagination Controls */}
                                {totalPages > 1 && (
                                    <div className="flex justify-center items-center gap-2 mt-6">
                                        <button
                                            className="btn btn-sm btn-ghost"
                                            onClick={() => setCurrentPage((p) => Math.max(1, p - 1))}
                                            disabled={currentPage === 1}
                                        >
                                            Previous
                                        </button>
                                        {Array.from({ length: totalPages }, (_, i) => (
                                            <button
                                                key={i}
                                                className={`btn btn-sm ${currentPage === i + 1 ? 'btn-primary' : 'btn-ghost'}`}
                                                onClick={() => setCurrentPage(i + 1)}
                                            >
                                                {i + 1}
                                            </button>
                                        ))}
                                        <button
                                            className="btn btn-sm btn-ghost"
                                            onClick={() => setCurrentPage((p) => Math.min(totalPages, p + 1))}
                                            disabled={currentPage === totalPages}
                                        >
                                            Next
                                        </button>
                                    </div>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}