<template>
  <AuthenticatedLayout :user="auth.user">
    <Head title="Stracker" />
    
    <div class="min-h-screen w-full flex flex-col md:flex-row bg-gray-100 text-gray-900">
      <aside class="w-full md:w-60 flex md:flex-col flex-row gap-2 md:gap-6 items-stretch min-h-[4rem] md:min-h-[32rem] justify-start bg-white border-b md:border-b-0 md:border-r border-gray-200 p-4 sticky top-0 z-20 shadow-sm">
        <div class="flex items-center md:mb-8 mb-2">
          <img src="/public/images/stracker-logo.png" alt="Stracker Logo" class="h-10 w-auto mr-3" />
          <span class="font-bold text-2xl text-gray-800 tracking-tight">Stracker</span>
        </div>
        
        <button
          :class="[tab === 'dashboard' ? 'bg-gray-900 text-white shadow' : 'bg-white text-gray-700 hover:bg-gray-100', 'rounded-lg px-4 py-2 font-medium transition']"
          @click="selectTab('dashboard')"
        >
          Dashboard
        </button>
        
        <button
          :class="[tab === 'transactions' ? 'bg-gray-900 text-white shadow' : 'bg-white text-gray-700 hover:bg-gray-100', 'rounded-lg px-4 py-2 font-medium transition']"
          @click="selectTab('transactions')"
        >
          Recent Transactions
        </button>
        
        <button
          class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 font-medium transition shadow"
          @click="showAddTransaction = true"
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
        <template v-if="tab === 'dashboard'">
          <div v-if="tabLoading.dashboard" class="space-y-8 animate-pulse">
            <!-- Summary Cards Skeleton -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 w-full">
              <div class="bg-white rounded-2xl shadow p-8 border border-gray-100 w-full">
                <div class="h-4 bg-gray-200 rounded w-1/3 mb-4"></div>
                <div class="h-8 bg-gray-200 rounded w-1/2"></div>
              </div>
              <div class="bg-white rounded-2xl shadow p-8 border border-gray-100 w-full">
                <div class="h-4 bg-gray-200 rounded w-1/3 mb-4"></div>
                <div class="h-8 bg-gray-200 rounded w-1/2"></div>
              </div>
              <div class="bg-white rounded-2xl shadow p-8 border border-gray-100 w-full">
                <div class="h-4 bg-gray-200 rounded w-1/3 mb-4"></div>
                <div class="h-8 bg-gray-200 rounded w-1/2"></div>
              </div>
            </div>

            <!-- Chart Filters Skeleton -->
            <div class="bg-white rounded-2xl shadow border border-gray-100 mb-8 w-full p-6">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2">
                <div class="h-5 bg-gray-200 rounded w-32"></div>
                <div class="h-4 bg-gray-200 rounded w-16"></div>
              </div>
              <div class="flex flex-col sm:flex-row flex-wrap gap-4 items-stretch">
                <div class="h-10 bg-gray-200 rounded w-full sm:w-40"></div>
                <div class="h-10 bg-gray-200 rounded w-full sm:w-40"></div>
                <div class="h-10 bg-gray-200 rounded w-full sm:w-40"></div>
                <div class="h-10 bg-gray-200 rounded w-full sm:w-40"></div>
              </div>
            </div>

            <!-- Charts Skeleton -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full">
              <div class="bg-white rounded-2xl shadow border border-gray-100 h-96 p-8 w-full flex flex-col gap-4">
                <div class="h-5 bg-gray-200 rounded w-40"></div>
                <div class="flex-1 bg-gray-100 rounded"></div>
              </div>
              <div class="bg-white rounded-2xl shadow border border-gray-100 h-96 p-8 w-full flex flex-col gap-4">
                <div class="h-5 bg-gray-200 rounded w-40"></div>
                <div class="flex-1 bg-gray-100 rounded"></div>
              </div>
            </div>
          </div>

          <div v-else class="space-y-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 w-full">
              <div class="bg-white rounded-2xl shadow p-8 flex flex-col items-center border border-gray-100">
                <div class="text-gray-500 text-base font-medium mb-2">Total Income</div>
                <div class="text-blue-600 text-3xl font-bold tracking-tight">₱{{ summary.income }}</div>
              </div>
              <div class="bg-white rounded-2xl shadow p-8 flex flex-col items-center border border-gray-100">
                <div class="text-gray-500 text-base font-medium mb-2">Total Expenses</div>
                <div class="text-red-600 text-3xl font-bold tracking-tight">₱{{ summary.expense }}</div>
              </div>
              <div class="bg-white rounded-2xl shadow p-8 flex flex-col items-center border border-gray-100">
                <div class="text-gray-500 text-base font-medium mb-2">Total Revenue</div>
                <div class="text-green-600 text-3xl font-bold tracking-tight">₱{{ summary.balance }}</div>
              </div>
            </div>

            <!-- Chart Filters -->
            <div class="bg-white rounded-2xl shadow border border-gray-100 mb-8 w-full p-6">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2">
                <h4 class="font-semibold text-gray-800 text-lg">Chart Filters</h4>
                <button type="button" class="text-blue-600 hover:underline text-sm font-medium" @click="clearChartFilters">
                  Clear All
                </button>
              </div>
              <div class="flex flex-col sm:flex-row flex-wrap gap-4 items-stretch">
                <div class="flex flex-col gap-1 w-full sm:w-auto">
                  <label class="font-semibold text-gray-700 mb-1" for="chart-type">Type</label>
                  <select id="chart-type" v-model="chartFilters.type" class="rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800 focus:border-blue-400 focus:ring-blue-400 text-sm">
                    <option value="">All</option>
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                  </select>
                </div>
                <div class="flex flex-col gap-1 w-full sm:w-auto">
                  <label class="font-semibold text-gray-700 mb-1" for="chart-category">Category</label>
                  <select
                    id="chart-category"
                    v-model="chartFilters.category"
                    :disabled="chartFilters.type === 'income'"
                    :class="[
                      'rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-400 focus:ring-blue-400',
                      chartFilters.type === 'income'
                        ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                        : 'bg-gray-50 text-gray-800'
                    ]"
                  >
                    <option value="">All</option>
                    <option
                      v-for="cat in chartCategoryOptions"
                      :key="cat"
                      :value="cat"
                    >
                      {{ cat }}
                    </option>
                  </select>
                </div>
                <div class="flex flex-col gap-1 w-full sm:w-auto">
                  <label class="font-semibold text-gray-700 mb-1" for="chart-date-from">From</label>
                  <input id="chart-date-from" type="date" v-model="chartFilters.date_from" class="rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800 focus:border-blue-400 focus:ring-blue-400 text-sm" />
                </div>
                <div class="flex flex-col gap-1 w-full sm:w-auto">
                  <label class="font-semibold text-gray-700 mb-1" for="chart-date-to">To</label>
                  <input id="chart-date-to" type="date" v-model="chartFilters.date_to" class="rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800 focus:border-blue-400 focus:ring-blue-400 text-sm" />
                </div>
              </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full">
              <!-- Line chart (left) -->
              <div>
                <div class="bg-white rounded-2xl shadow border border-gray-100 h-96 p-8 flex flex-col items-center justify-center w-full">
                  <h3 class="font-semibold mb-4 text-lg text-gray-800">Income vs Expense Over Time</h3>
                  <div class="w-full" style="min-height: 220px; height: 30vw; max-height: 340px;">
                    <LineChart :data="filteredLineData" />
                  </div>
                </div>
              </div>
              <!-- Pie chart (right) -->
              <div>
                <div class="bg-white rounded-2xl shadow border border-gray-100 h-96 p-8 flex flex-col items-center justify-center w-full">
                  <h3 class="font-semibold mb-4 text-lg text-center text-gray-800">Category Breakdown</h3>
                  <div v-if="filteredPieChartData.length > 0" class="w-full flex items-center justify-center" style="width: 380px; height: 380px; min-width: 280px; min-height: 280px; max-width: 280px; max-height: 280px;">
                    <PieChart :data="filteredPieChartData" :colors="pieChartColors" />
                  </div>
                  <p v-else class="text-gray-400 mt-10">Add transactions to see the diagram</p>
                </div>
              </div>
            </div>
            <!-- Pagination Controls -->
            <div v-if="transactions.meta && transactions.meta.links && transactions.meta.links.length > 1" class="flex justify-center mt-6">
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
        </template>

        <template v-else-if="tab === 'transactions'">
          <div v-if="tabLoading.transactions" class="bg-white rounded-xl shadow-sm overflow-hidden animate-pulse">
            <div class="p-4 border-b flex flex-col md:flex-row gap-4 items-stretch md:items-end">
              <div class="h-8 bg-gray-200 rounded w-full md:w-40"></div>
              <div class="h-8 bg-gray-200 rounded w-full md:w-40"></div>
              <div class="h-8 bg-gray-200 rounded w-full md:w-40"></div>
              <div class="h-8 bg-gray-200 rounded w-full md:w-40"></div>
              <div class="h-8 bg-gray-200 rounded w-full md:w-24"></div>
            </div>
            <div class="overflow-x-auto">
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
                  <tr v-for="n in 5" :key="n" class="border-b">
                    <td class="p-4"><div class="h-4 bg-gray-200 rounded w-24"></div></td>
                    <td class="p-4"><div class="h-4 bg-gray-200 rounded w-40"></div></td>
                    <td class="p-4"><div class="h-4 bg-gray-200 rounded w-32"></div></td>
                    <td class="p-4"><div class="h-4 bg-gray-200 rounded w-24"></div></td>
                    <td class="p-4"><div class="h-4 bg-gray-200 rounded w-16"></div></td>
                    <td class="p-4 text-right"><div class="h-4 bg-gray-200 rounded w-16 ml-auto"></div></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div v-else class="bg-white rounded-xl shadow-sm overflow-hidden">
            <!-- Filters -->
            <div class="p-4 border-b flex flex-col md:flex-row gap-4 items-stretch md:items-end">
              <div class="flex flex-col gap-1 w-full md:w-40">
                <label class="font-semibold text-gray-700 mb-1" for="filter-type">Type</label>
                <select id="filter-type" v-model="filters.type" class="rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800 focus:border-blue-400 focus:ring-blue-400 text-sm">
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
                <input id="filter-date-from" type="date" v-model="filters.date_from" class="rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800 focus:border-blue-400 focus:ring-blue-400 text-sm" />
              </div>
              <div class="flex flex-col gap-1 w-full md:w-40">
                <label class="font-semibold text-gray-700 mb-1" for="filter-date-to">To</label>
                <input id="filter-date-to" type="date" v-model="filters.date_to" class="rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800 focus:border-blue-400 focus:ring-blue-400 text-sm" />
              </div>
              <div class="flex flex-col gap-1 w-full md:w-auto">
                <label class="invisible mb-1">Clear</label>
                <button @click="clearFilters" class="rounded-lg px-4 py-2 bg-gray-200 text-gray-700 font-medium hover:bg-gray-300 transition">Clear</button>
              </div>
            </div>
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
                    <td class="p-4 font-semibold" :class="t.type === 'income' ? 'text-green-600' : 'text-red-600'">
                      ${{ t.amount }}
                    </td>
                    <td class="p-4 uppercase text-xs font-bold">{{ t.type }}</td>
                    <td class="p-4 text-right">
                      <div class="flex justify-end gap-2">
                        <button @click="openEditTransaction(t)" class="p-2 hover:bg-blue-50 text-blue-500 rounded-full">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        </button>
                        <button @click="deleteTransaction(t.id)" class="p-2 hover:bg-red-50 text-red-400 rounded-full">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
          </div>
        </template>
      </main>

      <AddTransaction v-if="showAddTransaction" :categories="categories" @close="showAddTransaction = false" />
      <EditTransaction v-if="editTransaction" :transaction="editTransaction" :categories="categories" @close="editTransaction = null" />
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, defineProps, watch, onMounted } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';
import EditTransaction from './EditTransaction.vue';
import AddTransaction from './AddTransaction.vue';
import LineChart from '../Components/LineChart.vue';
import PieChart from '../Components/PieChart.vue';

function logout() {
  router.post(route('logout'), {}, {
    onSuccess: () => {
      router.visit(route('login'));
    }
  });
}

const showAddTransaction = ref(false);
const editTransaction = ref(null);

function openEditTransaction(transaction) {
  editTransaction.value = { ...transaction };
}

const props = defineProps({
  auth: Object,
  transactions: Object,
  summary: Object,
  categories: Array,
  expenseTotals: Array,
  incomeTotals: Array,
  filters: Object,
});

const tab = ref('dashboard');
const tabLoading = ref({
  dashboard: true,
  transactions: false,
});

function selectTab(target) {
  if (tab.value === target) {
    return;
  }

  tab.value = target;
  tabLoading.value[target] = true;

  setTimeout(() => {
    tabLoading.value[target] = false;
  }, 1500);
}

const filters = ref({ ...props.filters });

// Dashboard chart filters (independent from transactions filter)
const chartFilters = ref({
  type: '',
  category: '',
  date_from: '',
  date_to: ''
});

// Category lists without 'Salary' for expense-related filters
const expenseFilterCategories = computed(() => props.categories.filter(cat => cat !== 'Salary'));

// Chart category options: only show 'Salary' when type is income
const chartCategoryOptions = computed(() => {
  if (chartFilters.value.type === 'income') {
    return props.categories.filter(cat => cat === 'Salary');
  }

  // For 'All' or 'expense', hide 'Salary'
  return props.categories.filter(cat => cat !== 'Salary');
});

// When chart type is income, clear category so it doesn't filter by a stale value
watch(
  () => chartFilters.value.type,
  (newType) => {
    if (newType === 'income') {
      chartFilters.value.category = '';
    }
  }
);

function clearChartFilters() {
  chartFilters.value = { type: '', category: '', date_from: '', date_to: '' };
  router.get(route('dashboard'));
}

// Filtered transactions for dashboard charts
const filteredChartTransactions = computed(() => {
  return (props.transactions.data || []).filter(t => {
    if (chartFilters.value.type && t.type !== chartFilters.value.type) return false;
    if (chartFilters.value.category && t.category !== chartFilters.value.category) return false;
    if (chartFilters.value.date_from && t.entry_date < chartFilters.value.date_from) return false;
    if (chartFilters.value.date_to && t.entry_date > chartFilters.value.date_to) return false;
    return true;
  });
});

const filteredLineData = computed(() => {
  const dateMap = {};
  filteredChartTransactions.value.forEach((t) => {
    const date = t.entry_date;
    if (!dateMap[date]) dateMap[date] = { date, income: 0, expense: 0 };
    if (t.type === 'income') {
      dateMap[date].income += parseFloat(t.amount);
    } else {
      dateMap[date].expense += parseFloat(t.amount);
    }
  });
  return Object.values(dateMap).sort((a, b) => a.date.localeCompare(b.date));
});

const filteredPieChartData = computed(() => {
  let totalIncome = 0;
  let totalExpense = 0;
  filteredChartTransactions.value.forEach((t) => {
    if (t.type === 'income') {
      totalIncome += Number(t.amount);
    } else if (t.type === 'expense') {
      totalExpense += Number(t.amount);
    }
  });
  const totalRevenue = totalIncome - totalExpense;
  // Only show slices that are > 0
  const data = [];
  if (totalIncome > 0) data.push({ label: 'Total Income', value: totalIncome });
  if (totalExpense > 0) data.push({ label: 'Total Expense', value: totalExpense });
  // Revenue can be negative, but show it for completeness
  data.push({ label: 'Total Revenue', value: totalRevenue });
  return data;
});


const pieChartColors = computed(() => {
  // Map each label to its color, in the same order as filteredPieChartData
  const colorMap = {
    'Total Income': '#2563eb', // blue-600
    'Total Expense': '#dc2626', // red-600
    'Total Revenue': '#16a34a', // green-600
  };
  return filteredPieChartData.value.map(d => colorMap[d.label] || '#a3a3a3');
});

// Debounce utility
function debounce(fn, delay) {
  let timeout;
  return (...args) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => fn(...args), delay);
  };
}

const applyFilters = debounce(() => {
  const cleaned = Object.fromEntries(
    Object.entries(filters.value).filter(([_, v]) => v !== undefined && v !== null && v !== '')
  );
  router.get(route('transactions.recent'), cleaned);
}, 400);

// Watch for real-time filter changes
watch(filters, applyFilters, { deep: true });
function clearFilters() {
  filters.value = {};
  router.get(route('transactions.recent'));
}
function deleteTransaction(id) {
  if (confirm('Are you sure you want to delete this transaction?')) {
    router.delete(route('transactions.destroy', id));
  }
}

onMounted(() => {
  setTimeout(() => {
    tabLoading.value.dashboard = false;
  }, 1500);
});
</script>

<style scoped>

</style>
