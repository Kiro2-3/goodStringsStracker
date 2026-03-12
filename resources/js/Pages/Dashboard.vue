<template>
  <AuthenticatedLayout :user="auth.user">
    <Head title="Stracker" />
    
    <div class="min-h-screen w-full flex flex-col md:flex-row bg-gray-100 text-gray-900">
      <AppSidebar
        :user="auth.user"
        active-page="dashboard"
        @add-transaction="showAddTransaction = true"
      />

      <main class="flex-1 space-y-6 px-4 md:px-10 py-8 w-full">
        <template v-if="tab === 'dashboard'">
          <div v-if="tabLoading.dashboard" class="space-y-5 animate-pulse">
            <!-- Summary Cards Skeleton -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 w-full">
              <div class="bg-white rounded-xl shadow p-6 border border-gray-100 w-full">
                <div class="h-3 bg-gray-200 rounded w-1/3 mb-3"></div>
                <div class="h-7 bg-gray-200 rounded w-1/2"></div>
              </div>
              <div class="bg-white rounded-xl shadow p-6 border border-gray-100 w-full">
                <div class="h-3 bg-gray-200 rounded w-1/3 mb-3"></div>
                <div class="h-7 bg-gray-200 rounded w-1/2"></div>
              </div>
              <div class="bg-white rounded-xl shadow p-6 border border-gray-100 w-full">
                <div class="h-3 bg-gray-200 rounded w-1/3 mb-3"></div>
                <div class="h-7 bg-gray-200 rounded w-1/2"></div>
              </div>
            </div>

            <!-- Chart Filters Skeleton -->
            <div class="bg-white rounded-xl shadow border border-gray-100 w-full p-4">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3 gap-2">
                <div class="h-4 bg-gray-200 rounded w-32"></div>
                <div class="h-3 bg-gray-200 rounded w-16"></div>
              </div>
              <div class="flex flex-col sm:flex-row flex-wrap gap-3 items-stretch">
                <div class="h-8 bg-gray-200 rounded w-full sm:w-36"></div>
                <div class="h-8 bg-gray-200 rounded w-full sm:w-36"></div>
                <div class="h-8 bg-gray-200 rounded w-full sm:w-36"></div>
                <div class="h-8 bg-gray-200 rounded w-full sm:w-36"></div>
              </div>
            </div>

            <!-- Charts Skeleton -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
              <div class="bg-white rounded-xl shadow border border-gray-100 h-64 p-5 w-full flex flex-col gap-3">
                <div class="h-4 bg-gray-200 rounded w-40"></div>
                <div class="flex-1 bg-gray-100 rounded"></div>
              </div>
              <div class="bg-white rounded-xl shadow border border-gray-100 h-64 p-5 w-full flex flex-col gap-3">
                <div class="h-4 bg-gray-200 rounded w-40"></div>
                <div class="flex-1 bg-gray-100 rounded"></div>
              </div>
            </div>
          </div>

          <div v-else class="space-y-5">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 w-full">
              <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center border border-gray-100">
                <div class="text-gray-500 text-sm font-medium mb-1">Total Income</div>
                <div class="text-blue-600 text-3xl font-bold tracking-tight">₱{{ summaryForDisplay.income }}</div>
              </div>
              <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center border border-gray-100">
                <div class="text-gray-500 text-sm font-medium mb-1">Total Expenses</div>
                <div class="text-red-600 text-3xl font-bold tracking-tight">₱{{ summaryForDisplay.expense }}</div>
              </div>
              <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center border border-gray-100">
                <div class="text-gray-500 text-sm font-medium mb-1">Total Revenue</div>
                <div class="text-green-600 text-3xl font-bold tracking-tight">₱{{ summaryForDisplay.balance }}</div>
              </div>
            </div>

            <!-- Chart Filters -->
            <div class="card border border-base-200 bg-base-100 shadow-sm w-full">
              <div class="card-body p-6">
                <div class="flex flex-wrap items-center gap-4 md:grid md:grid-cols-2 xl:grid-cols-4">
                  <label class="form-control w-full gap-1.5">
                    <span class="label-text text-sm font-semibold text-base-content">Type</span>
                    <select id="chart-type" v-model="chartFilters.type" class="select select-bordered w-full bg-base-100 text-base-content">
                      <option value="">All</option>
                      <option value="income">Income</option>
                      <option value="expense">Expense</option>
                    </select>
                  </label>

                  <label class="form-control w-full gap-1.5">
                    <span class="label-text text-sm font-semibold text-base-content">Category</span>
                    <select
                      id="chart-category"
                      v-model="chartFilters.category"
                      :disabled="chartFilters.type === 'income'"
                      :class="[
                        'select select-bordered w-full bg-base-100 text-base-content',
                        chartFilters.type === 'income' ? 'select-disabled opacity-60' : ''
                      ]"
                    >
                      <option value="">All</option>
                      <option v-for="cat in chartCategoryOptions" :key="cat" :value="cat">{{ cat }}</option>
                    </select>
                  </label>

                  <label class="form-control w-full gap-1.5">
                    <span class="label-text text-sm font-semibold text-base-content">From</span>
                    <input id="chart-date-from" v-model="chartFilters.date_from" type="date" class="input input-bordered w-full bg-base-100 text-base-content" />
                  </label>

                  <label class="form-control w-full gap-1.5">
                    <span class="label-text text-sm font-semibold text-base-content">To</span>
                    <input id="chart-date-to" v-model="chartFilters.date_to" type="date" class="input input-bordered w-full bg-base-100 text-base-content" />
                  </label>
                </div>
                <div class="flex justify-end mt-3">
                  <button type="button" class="btn btn-ghost btn-sm" @click="clearChartFilters">Clear All</button>
                </div>
              </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 gap-4 xl:grid-cols-5 w-full">
              <div class="xl:col-span-3">
                <div class="card border border-base-200 bg-base-100 shadow-sm">
                  <div class="card-body gap-4 p-5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                      <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-lg font-semibold text-base-content">Income vs Expense Over Time</h3>
                        <span class="badge badge-primary badge-outline badge-sm">Trend Overview</span>
                      </div>
     

                    </div>

                    <div class="grid grid-cols-2 gap-2">
                      <div class="rounded-xl border border-success/20 bg-success/5 px-3 py-2">
                        <p class="text-xs font-semibold uppercase tracking-widest text-success/80">Income Signal</p>
                        <p class="mt-0.5 text-base font-semibold text-base-content">₱{{ formatCurrency(summaryForDisplay.income) }}</p>
                      </div>
                      <div class="rounded-xl border border-error/20 bg-error/5 px-3 py-2">
                        <p class="text-xs font-semibold uppercase tracking-widest text-error/80">Expense Signal</p>
                        <p class="mt-0.5 text-base font-semibold text-base-content">₱{{ formatCurrency(summaryForDisplay.expense) }}</p>
                      </div>
                    </div>

                    <div class="rounded-2xl border border-base-200 bg-gradient-to-br from-primary/5 via-base-100 to-secondary/10 p-3">
                      <div class="h-[270px] w-full">
                        <LineChart :data="filteredLineData" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="xl:col-span-2">
                <div class="card border border-base-200 bg-base-100 shadow-sm h-full">
                  <div class="card-body gap-4 p-5">
                    <div class="flex flex-wrap items-center gap-2">
                      <h3 class="text-lg font-semibold text-base-content">Category Breakdown</h3>
                      <span class="badge badge-secondary badge-outline badge-sm">Spending Mix</span>
                    </div>

                    <div v-if="filteredPieChartData.length > 0" class="flex flex-col gap-3">
                      <div class="mx-auto flex h-[240px] w-full max-w-[240px] items-center justify-center rounded-2xl border border-base-200 bg-gradient-to-br from-secondary/10 via-base-100 to-primary/5 p-3">
                        <div class="h-full w-full">
                          <PieChart :data="filteredPieChartData" :colors="pieChartColors" />
                        </div>
                      </div>

                      <div class="space-y-1.5">
                        <div
                          v-for="(slice, index) in filteredPieChartData"
                          :key="slice.label"
                          class="flex items-center justify-between rounded-xl border border-base-200 bg-base-200/40 px-3 py-2"
                        >
                          <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full shrink-0" :style="{ backgroundColor: pieChartColors[index] || '#94a3b8' }"></span>
                            <p class="text-sm font-medium text-base-content">{{ slice.label }}</p>
                          </div>
                          <span class="text-sm font-semibold text-base-content">₱{{ formatCurrency(slice.value) }}</span>
                        </div>
                      </div>
                    </div>

                    <div v-else class="flex min-h-[200px] items-center justify-center rounded-2xl border border-dashed border-base-300 bg-base-200/40 text-center">
                      <div class="space-y-1 px-4">
                        <p class="text-sm font-semibold text-base-content">No chart data yet</p>
                        <p class="text-xs text-base-content/60">Add transactions to generate the breakdown view.</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
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
import AppSidebar from '@/Components/AppSidebar.vue';
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
  transactionCategories: Array,
  expenseTotals: Array,
  incomeTotals: Array,
  chartTransactions: Array,
});

const tab = ref('dashboard');
const tabLoading = ref({ dashboard: true });

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

// Dashboard chart filters
const chartFilters = ref({
  type: '',
  category: '',
  date_from: '',
  date_to: ''
});

const activeChartFilterCount = computed(() => {
  return Object.values(chartFilters.value).filter((value) => value !== '').length;
});

// All user categories from the Categories table
const expenseFilterCategories = computed(() => props.categories);

// Chart category options: only show categories used in actual transactions
const chartCategoryOptions = computed(() => props.transactionCategories ?? props.categories);

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
  return (props.chartTransactions || []).filter((t) => {
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

const summaryForDisplay = computed(() => {
  let income = 0;
  let expense = 0;

  filteredChartTransactions.value.forEach((t) => {
    if (t.type === 'income') {
      income += Number(t.amount);
    } else if (t.type === 'expense') {
      expense += Number(t.amount);
    }
  });

  const balance = income - expense;

  return {
    income: income.toFixed(2),
    expense: expense.toFixed(2),
    balance: balance.toFixed(2),
  };
});

const chartDateRangeLabel = computed(() => {
  const { date_from: dateFrom, date_to: dateTo } = chartFilters.value;

  if (dateFrom && dateTo) {
    return `${dateFrom} → ${dateTo}`;
  }

  if (dateFrom) {
    return `From ${dateFrom}`;
  }

  if (dateTo) {
    return `Until ${dateTo}`;
  }

  return 'All recorded dates';
});

function formatCurrency(amount) {
  return new Intl.NumberFormat('en-PH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(Number(amount || 0));
}


const pieChartColors = computed(() => {
  // Map each label to its color, in the same order as filteredPieChartData
  const colorMap = {
    'Total Income': '#2563eb', // blue-600
    'Total Expense': '#dc2626', // red-600
    'Total Revenue': '#16a34a', // green-600
  };
  return filteredPieChartData.value.map(d => colorMap[d.label] || '#a3a3a3');
});

function deleteTransaction(id) {
  if (confirm('Are you sure you want to delete this transaction?')) {
    router.delete(route('transactions.destroy', id));
  }
}

onMounted(() => {
  setTimeout(() => {
    tabLoading.value[tab.value] = false;
  }, 1500);
});
</script>

<style scoped>

</style>
