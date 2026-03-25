<template>
  <AuthenticatedLayout :user="auth.user">
    <Head title="Stracker" />
    
    <div class="min-h-screen w-full flex flex-col md:flex-row bg-base-200 text-base-content">
      <AppSidebar
        :user="auth.user"
        active-page="dashboard"
        @add-transaction="showAddTransaction = true"
      />

      <main class="flex-1 min-w-0 space-y-6 px-4 md:px-10 py-8">
        <template v-if="tab === 'dashboard'">
          <div v-if="tabLoading.dashboard" class="space-y-5 animate-pulse">
            <!-- Summary Cards Skeleton -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 w-full">
              <div class="rounded-xl shadow p-6 border border-base-200 bg-base-100 w-full">
                <div class="h-3 bg-base-200 rounded w-1/3 mb-3"></div>
                <div class="h-7 bg-base-200 rounded w-1/2"></div>
              </div>
              <div class="rounded-xl shadow p-6 border border-base-200 bg-base-100 w-full">
                <div class="h-3 bg-base-200 rounded w-1/3 mb-3"></div>
                <div class="h-7 bg-base-200 rounded w-1/2"></div>
              </div>
              <div class="rounded-xl shadow p-6 border border-base-200 bg-base-100 w-full">
                <div class="h-3 bg-base-200 rounded w-1/3 mb-3"></div>
                <div class="h-7 bg-base-200 rounded w-1/2"></div>
              </div>
            </div>

            <!-- Chart Filters Skeleton -->
            <div class="rounded-xl shadow border border-base-200 w-full p-4 bg-base-100">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3 gap-2">
                <div class="h-4 bg-gray-200 rounded w-32"></div>
                <div class="h-3 bg-gray-200 rounded w-16"></div>
              </div>
              <div class="flex flex-col sm:flex-row flex-wrap gap-3 items-stretch">
                <div class="h-8 bg-base-200 rounded w-full sm:w-36"></div>
                <div class="h-8 bg-base-200 rounded w-full sm:w-36"></div>
                <div class="h-8 bg-base-200 rounded w-full sm:w-36"></div>
                <div class="h-8 bg-base-200 rounded w-full sm:w-36"></div>
              </div>
            </div>

            <!-- Charts Skeleton -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
              <div class="rounded-xl shadow border border-base-200 h-64 p-5 w-full flex flex-col gap-3 bg-base-100">
                <div class="h-4 bg-base-200 rounded w-40"></div>
                <div class="flex-1 bg-base-200 rounded"></div>
              </div>
              <div class="rounded-xl shadow border border-base-200 h-64 p-5 w-full flex flex-col gap-3 bg-base-100">
                <div class="h-4 bg-base-200 rounded w-40"></div>
                <div class="flex-1 bg-base-200 rounded"></div>
              </div>
            </div>
          </div>

          <div v-else class="space-y-5">
            <!-- Chart Filters (moved to top) -->
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

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 w-full">
              <div class="card border border-base-200 bg-base-100 shadow-sm flex flex-col items-center p-6">
                <div class="text-base-content/60 text-sm font-medium mb-1">Total Income</div>
                <div class="text-success text-3xl font-bold tracking-tight">{{ currencySymbol }}{{ formatCurrency(summaryForDisplay.income) }}</div>
              </div>
              <div class="card border border-base-200 bg-base-100 shadow-sm flex flex-col items-center p-6">
                <div class="text-base-content/60 text-sm font-medium mb-1">Total Expenses</div>
                <div class="text-error text-3xl font-bold tracking-tight">{{ currencySymbol }}{{ formatCurrency(summaryForDisplay.expense) }}</div>
              </div>
              <div class="card border border-base-200 bg-base-100 shadow-sm flex flex-col items-center p-6">
                <div class="text-base-content/60 text-sm font-medium mb-1">Total Revenue</div>
                <div
                  class="text-3xl font-bold tracking-tight"
                  :class="Number(summaryForDisplay.balance) < 0 ? 'text-red-600' : 'text-green-600'"
                >{{ currencySymbol }}{{ formatCurrency(summaryForDisplay.balance) }}</div>
              </div>
            </div>

            <!-- Recent Transactions + Top Expense Categories row -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 w-full">
              <!-- Recent Transactions Card -->
              <div class="card border border-base-200 bg-base-100 shadow-sm">
                <div class="card-body p-5 gap-4">
                  <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                      <h3 class="text-lg font-semibold text-base-content">Recent Transactions</h3>
                      <span class="badge badge-primary badge-outline badge-sm">Last 5</span>
                    </div>
                    <button
                      type="button"
                      class="btn btn-ghost btn-xs text-primary"
                      @click="router.get(route('transactions.recent'))"
                    >View all →</button>
                  </div>

                  <div v-if="recentTransactions.length > 0" class="space-y-2">
                    <div
                      v-for="t in recentTransactions"
                      :key="t.id"
                      class="flex items-center gap-3 rounded-xl border border-base-200 bg-base-200/40 px-3 py-2.5"
                    >
                      <!-- type icon -->
                      <div
                        :class="[
                          'shrink-0 flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold',
                          t.type === 'income' ? 'bg-success/10 text-success' : 'bg-error/10 text-error',
                        ]"
                      >
                        <svg v-if="t.type === 'income'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m-7 7 7-7 7 7" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7-7 7-7-7" />
                        </svg>
                      </div>
                      <!-- description + meta -->
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-base-content truncate">{{ t.description }}</p>
                        <p class="text-xs text-base-content/50">
                          <span :class="t.category === 'Salary' ? 'text-green-600 font-semibold' : ''">{{ t.category }}</span>
                          · {{ t.entry_date }}
                        </p>
                      </div>
                      <!-- amount -->
                      <span
                        class="shrink-0 text-sm font-semibold"
                        :class="t.type === 'income' ? 'text-green-600' : 'text-error'"
                      >
                        {{ t.type === 'income' ? '+' : '-' }}{{ currencySymbol }}{{ formatCurrency(t.amount) }}
                      </span>
                    </div>
                  </div>

                  <div v-else class="flex min-h-[80px] items-center justify-center rounded-2xl border border-dashed border-base-300 bg-base-200/40 text-center">
                    <p class="text-sm text-base-content/60">No transactions yet.</p>
                  </div>
                </div>
              </div>

              <!-- Top Expense Categories Card -->
              <div class="card border border-base-200 bg-base-100 shadow-sm">
                <div class="card-body p-5 gap-4">
                  <div class="flex flex-wrap items-center gap-2">
                    <h3 class="text-lg font-semibold text-base-content">Top Expense Categories</h3>
                    <span class="badge badge-error badge-outline badge-sm">All Time</span>
                  </div>

                  <div v-if="topExpenseCategories.length > 0" class="space-y-3">
                    <div
                      v-for="(item, index) in topExpenseCategories"
                      :key="item.category"
                      class="flex items-center gap-3"
                    >
                      <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-error/10 text-xs font-bold text-error">
                        {{ index + 1 }}
                      </div>
                      <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                          <span class="text-sm font-medium text-base-content truncate">{{ item.category }}</span>
                          <span class="ml-3 shrink-0 text-sm font-semibold text-error">{{ currencySymbol }}{{ formatCurrency(item.total) }}</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full bg-base-200">
                          <div
                            class="h-1.5 rounded-full bg-error/70 transition-all duration-500"
                            :style="{ width: (Number(item.total) / Number(topExpenseCategories[0].total) * 100).toFixed(1) + '%' }"
                          ></div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div v-else class="flex min-h-[80px] items-center justify-center rounded-2xl border border-dashed border-base-300 bg-base-200/40 text-center">
                    <p class="text-sm text-base-content/60">No expense transactions yet.</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Charts -->

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
                        <p class="mt-0.5 text-base font-semibold text-base-content">{{ currencySymbol }}{{ formatCurrency(summaryForDisplay.income) }}</p>
                      </div>
                      <div class="rounded-xl border border-error/20 bg-error/5 px-3 py-2">
                        <p class="text-xs font-semibold uppercase tracking-widest text-error/80">Expense Signal</p>
                        <p class="mt-0.5 text-base font-semibold text-base-content">{{ currencySymbol }}{{ formatCurrency(summaryForDisplay.expense) }}</p>
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
                          <span class="text-sm font-semibold text-base-content">{{ currencySymbol }}{{ formatCurrency(slice.value) }}</span>
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
import { computed, onMounted, ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import AppSidebar from '@/Components/AppSidebar.vue'
import EditTransaction from '@/Pages/EditTransaction.vue'
import AddTransaction from '@/Pages/AddTransaction.vue'
import LineChart from '@/Components/LineChart.vue'
import PieChart from '@/Components/PieChart.vue'
import { useCurrency } from '@/composables/useCurrency.js'

const { currencySymbol } = useCurrency()

// Maps pie chart slice labels to fixed brand colours for consistency
const PIE_CHART_COLOR_MAP = {
  'Total Income':  '#2563eb',
  'Total Expense': '#dc2626',
  'Total Revenue': '#16a34a',
}

const props = defineProps({
  auth:                 Object,
  transactions:         Object,
  summary:              Object,           // pre-computed totals from server (income, expense, balance)
  categories:           Array,
  transactionCategories: Array,           // distinct categories present in the user's transactions
  expenseTotals:        Array,
  incomeTotals:         Array,
  chartTransactions:    Array,            // full transaction list used for client-side chart filtering
  topExpenseCategories: { type: Array, default: () => [] },  // top 5 expense categories by total
  recentTransactions:   { type: Array, default: () => [] },  // last 5 transactions (shared globally)
})

const showAddTransaction = ref(false)
const editTransaction    = ref(null)    // holds the transaction being edited; null hides the modal
const tab                = ref('dashboard')
const tabLoading         = ref({ dashboard: true })  // controls the animated skeleton while loading

// Client-side filter state for the charts (does NOT trigger a server request)
const chartFilters = ref({
  type:      '',
  category:  '',
  date_from: '',
  date_to:   '',
})

function openEditTransaction(transaction) {
  editTransaction.value = { ...transaction }
}

function logout() {
  router.post(route('logout'), {}, {
    onSuccess: () => {
      router.visit(route('login'))
    },
  })
}

function selectTab(target) {
  // Avoid re-rendering if the tab is already active
  if (tab.value === target) {
    return
  }
  tab.value = target
  tabLoading.value[target] = true
  // Short delay simulates a loading phase for a smoother perceived transition
  setTimeout(() => {
    tabLoading.value[target] = false
  }, 1500)
}

// Count of active non-empty filter values; used to show a "filters active" badge
const activeChartFilterCount = computed(() => {
  return Object.values(chartFilters.value).filter((value) => value !== '').length
})

// Use the distinct categories from the user's own transactions when available
const chartCategoryOptions = computed(() => props.transactionCategories ?? props.categories)

// When the type is switched to income, clear the category filter (income has no sub-categories)
watch(
  () => chartFilters.value.type,
  (newType) => {
    if (newType === 'income') {
      chartFilters.value.category = ''
    }
  },
)

// Resets all chart filters and reloads the dashboard from the server
function clearChartFilters() {
  chartFilters.value = { type: '', category: '', date_from: '', date_to: '' }
  router.get(route('dashboard'))
}

// Filters the raw transaction list client-side based on the chart filter state
const filteredChartTransactions = computed(() => {
  return (props.chartTransactions || []).filter((t) => {
    if (chartFilters.value.type && t.type !== chartFilters.value.type) return false
    if (chartFilters.value.category && t.category !== chartFilters.value.category) return false
    if (chartFilters.value.date_from && t.entry_date < chartFilters.value.date_from) return false
    if (chartFilters.value.date_to && t.entry_date > chartFilters.value.date_to) return false
    return true
  })
})

/**
 * Aggregates filtered transactions by date for the line chart.
 * Produces an array of { date, income, expense } sorted chronologically.
 */
const filteredLineData = computed(() => {
  const dateMap = {}
  filteredChartTransactions.value.forEach((t) => {
    const date = t.entry_date
    if (!dateMap[date]) dateMap[date] = { date, income: 0, expense: 0 }
    if (t.type === 'income') {
      dateMap[date].income += parseFloat(t.amount)
    } else {
      dateMap[date].expense += parseFloat(t.amount)
    }
  })
  return Object.values(dateMap).sort((a, b) => a.date.localeCompare(b.date))
})

/**
 * Builds the pie chart dataset with three slices: Income, Expense, Revenue.
 * Slices with zero income/expense are omitted to keep the chart clean.
 */
const filteredPieChartData = computed(() => {
  let totalIncome  = 0
  let totalExpense = 0

  filteredChartTransactions.value.forEach((t) => {
    if (t.type === 'income') {
      totalIncome += Number(t.amount)
    } else if (t.type === 'expense') {
      totalExpense += Number(t.amount)
    }
  })

  const totalRevenue = totalIncome - totalExpense
  const data = []

  if (totalIncome > 0) data.push({ label: 'Total Income', value: totalIncome })
  if (totalExpense > 0) data.push({ label: 'Total Expense', value: totalExpense })
  data.push({ label: 'Total Revenue', value: totalRevenue })

  return data
})

// Summary cards reflect the currently filtered dataset (not the server-side pre-computed summary)
const summaryForDisplay = computed(() => {
  let income  = 0
  let expense = 0

  filteredChartTransactions.value.forEach((t) => {
    if (t.type === 'income') {
      income += Number(t.amount)
    } else if (t.type === 'expense') {
      expense += Number(t.amount)
    }
  })

  const balance = income - expense

  return {
    income:  income.toFixed(2),
    expense: expense.toFixed(2),
    balance: balance.toFixed(2),
  }
})

// Human-readable label for the active date range shown in the chart header
const chartDateRangeLabel = computed(() => {
  const { date_from: dateFrom, date_to: dateTo } = chartFilters.value

  if (dateFrom && dateTo) return `${dateFrom} → ${dateTo}`
  if (dateFrom) return `From ${dateFrom}`
  if (dateTo) return `Until ${dateTo}`

  return 'All recorded dates'
})

// Maps each pie slice to its fixed colour via PIE_CHART_COLOR_MAP; grey fallback for unknowns
const pieChartColors = computed(() => {
  return filteredPieChartData.value.map((d) => PIE_CHART_COLOR_MAP[d.label] || '#a3a3a3')
})

// Formats a number as Philippine Peso with locale-aware thousand separators
function formatCurrency(amount) {
  return new Intl.NumberFormat('en-PH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(Number(amount || 0))
}

function deleteTransaction(id) {
  if (confirm('Are you sure you want to delete this transaction?')) {
    router.delete(route('transactions.destroy', id))
  }
}

// Simulate a 1.5 s load phase on initial mount to show the skeleton screens
onMounted(() => {
  setTimeout(() => {
    tabLoading.value[tab.value] = false
  }, 1500)
})



</script>

<style scoped>

</style>
