<template>
  <AuthenticatedLayout :user="auth.user">
    <Head title="Recent Transactions" />
    <div class="min-h-screen w-full flex flex-col md:flex-row bg-gray-100 text-gray-900">
      <aside
        class="w-full md:w-60 flex md:flex-col flex-row gap-2 md:gap-6 items-stretch min-h-[4rem] md:min-h-[32rem] justify-start bg-white border-b md:border-b-0 md:border-r border-gray-200 p-4 sticky top-0 z-20 shadow-sm"
      >
        <div class="flex items-center md:mb-8 mb-2">
          <img src="/public/images/stracker-logo.png" alt="Stracker Logo" class="h-10 w-auto mr-3" />
          <span class="font-bold text-2xl text-gray-800 tracking-tight">Stracker</span>
        </div>

        <button
          :class="['bg-white text-gray-700 hover:bg-gray-100', 'rounded-lg px-4 py-2 font-medium transition']"
          @click="goToDashboard"
        >
          Dashboard
        </button>

        <button
          :class="['bg-gray-900 text-white shadow', 'rounded-lg px-4 py-2 font-medium transition']"
        >
          Recent Transactions
        </button>

        <button
          class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 font-medium transition shadow"
          @click="goToAddTransaction"
        >
          Add Transaction
        </button>

        <button
          @click="logout"
          class="bg-red-50 text-red-600 hover:bg-red-100 rounded-lg px-4 py-2 font-semibold transition mt-auto border border-red-100"
        >
          Logout
        </button>
      </aside>

      <main class="flex-1 space-y-10 px-4 md:px-12 py-8 w-full">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
          <!-- Filters -->
          <div class="p-4 border-b flex flex-col md:flex-row gap-4 items-stretch md:items-end">
            <div class="flex flex-col gap-1 w-full md:w-40">
              <label class="font-semibold text-gray-700 mb-1" for="filter-type">Type</label>
              <select
                id="filter-type"
                v-model="filters.type"
                class="rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800 focus:border-blue-400 focus:ring-blue-400 text-sm"
              >
                <option value="">All</option>
                <option value="income">Income</option>
                <option value="expense">Expense</option>
              </select>
            </div>
            <div class="flex flex-col gap-1 w-full md:w-40">
              <label class="font-semibold text-gray-700 mb-1" for="filter-category">Category</label>
              <select
                id="filter-category"
                v-model="filters.category"
                :disabled="filters.type === 'income'"
                :class="[
                  'rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-400 focus:ring-blue-400',
                  filters.type === 'income'
                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                    : 'bg-gray-50 text-gray-800'
                ]"
              >
                <option value="">All</option>
                <option
                  v-for="cat in (filters.type === 'expense' ? expenseFilterCategories : categories)"
                  :key="cat"
                  :value="cat"
                >
                  {{ cat }}
                </option>
              </select>
            </div>
            <div class="flex flex-col gap-1 w-full md:w-40">
              <label class="font-semibold text-gray-700 mb-1" for="filter-date-from">From</label>
              <input
                id="filter-date-from"
                type="date"
                v-model="filters.date_from"
                class="rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800 focus:border-blue-400 focus:ring-blue-400 text-sm"
              />
            </div>
            <div class="flex flex-col gap-1 w-full md:w-40">
              <label class="font-semibold text-gray-700 mb-1" for="filter-date-to">To</label>
              <input
                id="filter-date-to"
                type="date"
                v-model="filters.date_to"
                class="rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800 focus:border-blue-400 focus:ring-blue-400 text-sm"
              />
            </div>
            <div class="flex flex-col gap-1 w-full md:w-auto">
              <label class="invisible mb-1">Clear</label>
              <button
                @click="clearFilters"
                class="rounded-lg px-4 py-2 bg-gray-200 text-gray-700 font-medium hover:bg-gray-300 transition"
              >
                Clear
              </button>
            </div>
          </div>

          <!-- Table -->
          <div class="overflow-x-auto max-h-[28rem] overflow-y-auto">
            <table class="w-full text-left">
              <thead class="bg-gray-50 border-b">
                <tr>
                  <th class="p-4">Date</th>
                  <th class="p-4">Description</th>
                  <th class="p-4">Category</th>
                  <th class="p-4">Amount</th>
                  <th class="p-4">Type</th>
                  <th class="p-4 text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="t in transactions.data" :key="t.id" class="border-b hover:bg-gray-50">
                  <td class="p-4">{{ t.entry_date }}</td>
                  <td class="p-4">{{ t.description }}</td>
                  <td class="p-4">{{ t.category }}</td>
                  <td
                    class="p-4 font-semibold"
                    :class="t.type === 'income' ? 'text-green-600' : 'text-red-600'"
                  >
                    ${{ t.amount }}
                  </td>
                  <td class="p-4 uppercase text-xs font-bold">{{ t.type }}</td>
                  <td class="p-4 text-right">
                    <div class="flex justify-end gap-2">
                      <button
                        @click="openEditTransaction(t)"
                        class="p-2 hover:bg-blue-50 text-blue-500 rounded-full"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                          />
                        </svg>
                      </button>
                      <button
                        @click="deleteTransaction(t.id)"
                        class="p-2 hover:bg-red-50 text-red-400 rounded-full"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                          />
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="!transactions.data || transactions.data.length === 0">
                  <td colspan="5" class="p-10 text-center text-gray-400">No transactions found yet.</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div
            v-if="transactions.meta && transactions.meta.links && transactions.meta.links.length > 1"
            class="flex justify-center mt-6"
          >
            <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
              <button
                v-for="link in transactions.meta.links"
                :key="link.label"
                :disabled="!link.url"
                @click="link.url && router.get(link.url)"
                v-html="link.label"
                class="px-4 py-2 border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium first:rounded-l-md last:rounded-r-md disabled:opacity-50 disabled:cursor-not-allowed"
                :class="{ 'bg-gray-200 text-gray-900 font-bold': link.active }"
              />
            </nav>
          </div>
        </div>

        <EditTransaction
          v-if="editTransaction"
          :transaction="editTransaction"
          :categories="categories"
          @close="editTransaction = null"
        />
      </main>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, defineProps, watch, computed } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';
import EditTransaction from './EditTransaction.vue';

const props = defineProps({
  auth: Object,
  transactions: Object,
  categories: Array,
  filters: Object,
});

const filters = ref({ ...props.filters });
const editTransaction = ref(null);

// Categories to use when filtering expenses (exclude 'Salary' if present)
const expenseFilterCategories = computed(() => props.categories.filter(cat => cat !== 'Salary'));

function openEditTransaction(transaction) {
  editTransaction.value = { ...transaction };
}

function goToDashboard() {
  router.get(route('dashboard'));
}

function goToAddTransaction() {
  router.get(route('transactions.add'));
}

function logout() {
  router.post(route('logout'), {}, {
    onSuccess: () => {
      router.visit(route('login'));
    },
  });
}

function clearFilters() {
  filters.value = {
    type: '',
    category: '',
    date_from: '',
    date_to: '',
  };

  router.get(route('transactions.recent'), {}, {
    preserveScroll: true,
    replace: true,
  });
}

watch(
  filters,
  (newFilters) => {
    router.get(route('transactions.recent'), newFilters, {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    });
  },
  { deep: true }
);

function deleteTransaction(id) {
  if (confirm('Are you sure you want to delete this transaction?')) {
    router.delete(route('transactions.destroy', id));
  }
}
</script>

<style scoped>
</style>
