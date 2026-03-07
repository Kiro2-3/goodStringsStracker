import React from 'react';
import { useForm, router } from '@inertiajs/react';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';

export default function AddTransaction({ categories = ["Food", "Rent", "Leisure", "Bills"] }) {
    const { data, setData, post, processing, reset, errors } = useForm({
        description: '',
        amount: '',
        type: 'expense',
        category: 'Food',
        entry_date: new Date().toISOString().split('T')[0],
    });

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

    return (
        <div className="min-h-screen w-full flex flex-col items-center justify-center bg-base-100 dark:bg-gray-900 px-4 py-12">
            <div className="w-full max-w-2xl card bg-white dark:bg-gray-800 shadow-xl p-10">
                <div className="mb-8 flex items-center justify-between">
                    <button
                        type="button"
                        className="btn btn-outline btn-lg"
                        onClick={() => router.visit(route('dashboard'))}
                    >
                        ← Back to Dashboard
                    </button>
                    <h2 className="font-bold text-3xl text-blue-600 dark:text-blue-300 text-right">Add Transaction</h2>
                </div>
                <form onSubmit={submit} className="space-y-8">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div className="form-control">
                            <InputLabel value="Description" htmlFor="description" />
                            <TextInput
                                id="description"
                                type="text"
                                placeholder="Description"
                                value={data.description}
                                onChange={(e) => setData('description', e.target.value)}
                            />
                            {errors.description && (
                                <InputError message={errors.description} />
                            )}
                        </div>
                        <div className="form-control">
                            <InputLabel value="Amount" htmlFor="amount" />
                            <TextInput
                                id="amount"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="0.00"
                                value={data.amount}
                                onChange={(e) => setData('amount', e.target.value)}
                            />
                            {errors.amount && (
                                <InputError message={errors.amount} />
                            )}
                        </div>
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div className="form-control">
                            <InputLabel value="Type" htmlFor="type" />
                            <select
                                id="type"
                                className="select select-bordered w-full text-black bg-white"
                                value={data.type}
                                onChange={(e) => setData('type', e.target.value)}
                            >
                                <option value="income">Income</option>
                                <option value="expense">Expense</option>
                            </select>
                            {errors.type && (
                                <InputError message={errors.type} />
                            )}
                        </div>
                        <div className="form-control">
                            <InputLabel value="Category" htmlFor="category" />
                            <select
                                id="category"
                                className={`select select-bordered w-full text-black bg-white ${data.type === 'income' ? 'opacity-50 cursor-not-allowed' : ''}`}
                                value={data.category}
                                onChange={(e) => setData('category', e.target.value)}
                                disabled={data.type === 'income'}
                            >
                                {data.type === 'income' ? (
                                    <option value="Salary">Salary</option>
                                ) : (
                                    categories.map((cat) => (
                                        <option key={cat} value={cat}>{cat}</option>
                                    ))
                                )}
                            </select>
                            {errors.category && (
                                <InputError message={errors.category} />
                            )}
                        </div>
                    </div>
                    <div className="form-control max-w-xs">
                        <InputLabel value="Date" htmlFor="entry_date" />
                        <input
                            id="entry_date"
                            type="date"
                            className="input input-bordered w-full text-black bg-white"
                            value={data.entry_date}
                            onChange={(e) => setData('entry_date', e.target.value)}
                        />
                        {errors.entry_date && (
                            <InputError message={errors.entry_date} />
                        )}
                    </div>
                    <div className="flex justify-end">
                        <PrimaryButton
                            type="submit"
                            className="w-full md:w-1/3 text-lg"
                            disabled={processing}
                        >
                            Save Transaction
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    );
}
