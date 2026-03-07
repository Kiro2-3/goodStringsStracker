import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, Link } from '@inertiajs/react';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';

// Edit transaction page - allows users to modify existing income or expense entries
export default function Edit({ auth, transaction }) {
    // useForm hook manages the form state and submission of the updated data
    const { data, setData, put, processing, errors } = useForm({
        description: transaction.description,
        amount: transaction.amount,
        type: transaction.type,
        category: transaction.category,
        entry_date: transaction.entry_date,
    });

    // Handle form submission - sends a PUT request to update the transaction
    const submit = (e) => {
        e.preventDefault();
        put(route('transactions.update', transaction.id));
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Edit Transaction" />

            <div className="py-12 px-4 max-w-2xl mx-auto">
                {/* Page Header */}
                <div className="mb-8">
                    <h1 className="text-3xl font-bold">Edit Transaction</h1>
                    <p className="mt-2 text-gray-700 dark:text-gray-400">Modify the transaction details below</p>
                </div>

                {/* Edit form card */}
                <div className="card bg-base-100 shadow">
                    <div className="card-body">
                        <form onSubmit={submit} className="space-y-6">
                            <div className="form-control">
                                <InputLabel value="Description" htmlFor="description" />
                                <TextInput
                                    id="description"
                                    type="text"
                                    placeholder="e.g., Monthly salary, Coffee purchase"
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                />
                                {errors.description && (
                                    <InputError message={errors.description} />
                                )}
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div className="form-control">
                                    <InputLabel value="Amount" htmlFor="amount" />
                                    <TextInput
                                        id="amount"
                                        type="number"
                                        placeholder="0.00"
                                        step="0.01"
                                        min="0"
                                        value={data.amount}
                                        onChange={(e) => setData('amount', e.target.value)}
                                    />
                                    {errors.amount && (
                                        <InputError message={errors.amount} />
                                    )}
                                </div>

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
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div className="form-control">
                                    <InputLabel value="Category" htmlFor="category" />
                                    <select
                                        id="category"
                                        className="select select-bordered w-full text-black bg-white"
                                        value={data.category}
                                        onChange={(e) => setData('category', e.target.value)}
                                    >
                                        <option value="Food">Food</option>
                                        <option value="Salary">Salary</option>
                                        <option value="Rent">Rent</option>
                                        <option value="Leisure">Leisure</option>
                                        <option value="Bills">Bills</option>
                                        <option value="Transportation">Transportation</option>
                                        <option value="Utilities">Utilities</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    {errors.category && (
                                        <InputError message={errors.category} />
                                    )}
                                </div>

                                <div className="form-control">
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
                            </div>

                            {/* Action buttons */}
                            <div className="flex gap-3 pt-6">
                                <PrimaryButton
                                    type="submit"
                                    className="flex-1"
                                    disabled={processing}
                                >
                                    {processing ? 'Saving...' : 'Save Changes'}
                                </PrimaryButton>

                                <Link
                                    href={route('dashboard')}
                                    className="btn btn-ghost flex-1"
                                >
                                    Cancel
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
