<template>
  <AuthenticatedLayout :user="auth.user">
    <Head title="Bank Accounts" />
    <div class="min-h-screen w-full flex flex-col md:flex-row bg-base-200 text-base-content">
      <div class="hidden md:block">
        <AppSidebar :user="auth.user" active-page="bank-accounts" />
      </div>
      <main class="flex-1 min-w-0 px-4 md:px-12 py-8">
        <div class="flex justify-end mb-6">
          <button
            @click="openAddModal"
            aria-label="Add Bank Account"
            class="rounded-full md:rounded shadow-lg px-4 py-3 text-white border-0 bg-gradient-to-r from-purple-600 to-amber-300 hover:from-purple-700 hover:to-amber-400"
          >
            Add Bank Account
          </button>
        </div>
        <div class="max-w-full mx-auto grid grid-cols-1 md:grid-cols-12 gap-8">
          <!-- Balance Bar Chart Card -->
          <div class="w-full md:col-span-8">
            <div class="card bg-base-100 border border-base-200 shadow p-4 mb-6 h-[520px] flex flex-col">
              <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold">Account Balances</h3>
                <div class="text-sm text-base-content/70">Total: {{ formatCurrency(props.totalBalance) }}</div>
              </div>
              <div class="flex-1 min-h-0">
                <canvas ref="balanceChart" class="w-full h-full block"></canvas>
              </div>
            </div>
          </div>

          <!-- Saved Bank Accounts Card -->
          <div class="w-full md:col-span-4 flex flex-col">
            <!-- Analytics Card -->
            <div class="card bg-base-100 border border-base-200 shadow p-6 mb-6">
              <h3 class="text-lg font-semibold mb-2">Wallet Summary</h3>
              <div class="flex items-center justify-between">
                <div>
                  <div class="text-sm text-base-content/70">Total Balance</div>
                  <div class="text-2xl font-bold">{{ formatCurrency(props.totalBalance) }}</div>
                </div>
                <div class="text-right">
                  <div class="text-sm text-base-content/70">Accounts</div>
                  <div class="text-lg font-medium">{{ props.bankAccounts && props.bankAccounts.total ? props.bankAccounts.total : (accounts.length || 0) }}</div>
                </div>
              </div>
            </div>

            <!-- Upcoming Recurring Payments Timeline Card -->
            <div class="card bg-base-100 border border-base-200 shadow p-4 mb-6">
              <h3 class="text-lg font-semibold mb-3">Upcoming Recurring Payments</h3>
              <div v-if="upcomingItems && upcomingItems.length" class="flex flex-col gap-3">
                <ul class="overflow-y-auto">
                  <li v-for="item in upcomingItems" :key="item.id" class="flex items-start gap-3 py-2">
                    <div class="w-16 text-xs text-base-content/60">{{ formatDate(item.date) }}</div>
                    <div class="flex-1">
                      <div class="flex items-center justify-between">
                        <div class="text-sm font-medium">{{ item.description || 'Subscription' }}</div>
                        <div class="text-sm font-semibold">{{ formatCurrency(item.amount) }}</div>
                      </div>
                      <div class="text-xs text-base-content/60">From: {{ item.account_name || getAccountName(item.account_id) || '—' }}</div>
                    </div>
                  </li>
                </ul>
              </div>
              <div v-else class="text-sm text-base-content/60">No recurring payments due in the next 7 days.</div>
            </div>

            <div v-if="accounts && accounts.length" class="card bg-base-100 border border-base-200 shadow p-4 h-[440px] flex flex-col">
              <h3 class="text-xl font-semibold mb-3">Saved Bank Accounts</h3>
              <div class="overflow-y-auto flex-1 pr-2">
                <ul class="divide-y divide-base-200">
                  <li v-for="account in accounts" :key="account.id" class="py-3 cursor-pointer hover:bg-base-200 rounded transition"
                      @click="handleAccountClick(account)">
                    <div class="flex flex-col gap-1">
                      <div class="flex items-center justify-between">
                        <span class="font-bold text-base-content">{{ account.bank_name }}</span>
                        <span class="text-xs text-base-content/60">{{ account.branch }}</span>
                      </div>
                      <div class="text-sm text-base-content/80">Acct #: {{ account.account_number }}</div>
                      <div class="text-sm text-base-content/80">Name: {{ account.account_name }}</div>
                      <div class="text-sm text-base-content/80">Balance: {{ formatCurrency(account.balance) }}</div>
                      <div v-if="account.notes" class="text-xs text-base-content/50 mt-1">{{ account.notes }}</div>
                    </div>
                  </li>
                </ul>
              </div>
              <nav v-if="props.bankAccounts && props.bankAccounts.links" class="mt-4 flex justify-center">
                <ul class="inline-flex items-center -space-x-px">
                  <li v-for="link in props.bankAccounts.links" :key="link.label">
                    <button
                      class="px-3 py-1 border rounded-l-md bg-base-100 hover:bg-base-200"
                      :class="{ 'bg-primary text-white': link.active, 'opacity-50 cursor-not-allowed': !link.url }"
                      @click="goToPage(link.url)"
                      v-html="link.label"
                      :disabled="!link.url"
                    ></button>
                  </li>
                </ul>
              </nav>
            </div>
            <div v-else class="card bg-base-100 border border-base-200 shadow p-6 text-center text-base-content/60 h-[440px] flex items-center justify-center">
              <span>No bank accounts saved yet.</span>
            </div>
          </div>

          <!-- Popup Modal for Bank Account Details (Editable) -->
          <transition name="fade">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" @click.self="closeModal">
              <div class="bg-base-100 rounded-lg shadow-2xl p-8 w-full max-w-md relative animate-popup">
                <button @click="closeModal" class="absolute top-2 right-2 text-base-content/60 hover:text-base-content text-xl">&times;</button>
                <h2 class="text-2xl font-bold mb-4">Edit Bank Account</h2>
                <form @submit.prevent="saveEdit" v-if="editAccount">
                  <div class="mb-2">
                    <label class="font-semibold">Bank Name:</label>
                    <input v-model="editAccount.bank_name" class="input input-bordered w-full" required />
                  </div>
                  <div class="mb-2">
                    <label class="font-semibold">Account Number:</label>
                    <input v-model="editAccount.account_number" class="input input-bordered w-full" required />
                  </div>
                  <div class="mb-2">
                    <label class="font-semibold">Account Name:</label>
                    <input v-model="editAccount.account_name" class="input input-bordered w-full" required />
                  </div>
                  <div class="mb-2">
                    <label class="font-semibold">Branch:</label>
                    <input v-model="editAccount.branch" class="input input-bordered w-full" />
                  </div>
                  <div class="mb-2">
                    <label class="font-semibold">Notes:</label>
                    <textarea v-model="editAccount.notes" class="textarea textarea-bordered w-full"></textarea>
                  </div>
                  <div class="mb-2">
                    <label class="font-semibold">Current Saving:</label>
                    <input v-model.number="editAccount.balance" type="number" step="0.01" class="input input-bordered w-full" />
                  </div>
                  <div class="mt-6 flex justify-end gap-2">
                    <button type="button" @click="confirmDelete" class="btn btn-error">Delete</button>
                    <div class="flex-1"></div>
                    <button type="button" @click="closeModal" class="btn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                  </div>
                </form>
              </div>
            </div>
          </transition>

          <!-- Add Bank Account Modal -->
          <transition name="fade">
            <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" @click.self="closeAddModal">
              <div class="bg-base-100 rounded-lg shadow-2xl p-6 w-full max-w-2xl relative animate-popup">
            <button @click="closeAddModal" class="absolute top-3 right-3 text-base-content/60 hover:text-base-content text-xl">&times;</button>
                <h2 class="text-2xl font-bold mb-4">Add Bank Account</h2>
                <form @submit.prevent="submitBankAccount">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="form-control w-full gap-2">
                      <span class="label-text font-semibold text-base-content">Bank Name</span>
                      <input v-model="form.bank_name" type="text" class="input input-bordered w-full bg-base-100 text-base-content" required />
                    </label>
                    <label class="form-control w-full gap-2">
                      <span class="label-text font-semibold text-base-content">Account Number</span>
                      <input v-model="form.account_number" type="text" class="input input-bordered w-full bg-base-100 text-base-content" required />
                    </label>
                    <label class="form-control w-full gap-2">
                      <span class="label-text font-semibold text-base-content">Account Name</span>
                      <input v-model="form.account_name" type="text" class="input input-bordered w-full bg-base-100 text-base-content" required />
                    </label>
                    <label class="form-control w-full gap-2">
                      <span class="label-text font-semibold text-base-content">Bank Branch</span>
                      <input v-model="form.branch" type="text" class="input input-bordered w-full bg-base-100 text-base-content" />
                    </label>
                    <label class="form-control md:col-span-2 w-full gap-2">
                      <span class="label-text font-semibold text-base-content">Notes</span>
                      <textarea v-model="form.notes" class="textarea textarea-bordered w-full bg-base-100 text-base-content" rows="2"></textarea>
                    </label>
                    <label class="form-control w-full gap-2">
                      <span class="label-text font-semibold text-base-content">Current Saving</span>
                      <input v-model.number="form.balance" type="number" step="0.01" class="input input-bordered w-full bg-base-100 text-base-content" />
                    </label>
                  </div>
                  <div class="mt-6 flex justify-end gap-2">
                    <button type="button" @click="closeAddModal" class="btn">Cancel</button>
                    <button type="submit" class="text-white border-0 px-4 py-2 rounded bg-gradient-to-r from-purple-600 to-amber-300 hover:from-purple-700 hover:to-amber-400">Save</button>
                  </div>
                </form>
              </div>
            </div>
          </transition>
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import AppSidebar from '@/Components/AppSidebar.vue'
import Chart from 'chart.js/auto'

const props = defineProps({
  auth: Object,
  bankAccounts: Object,
  totalBalance: Number,
  upcomingRecurring: {
    type: Array,
    default: () => [],
  },
})

const form = ref({
  bank_name: '',
  account_number: '',
  account_name: '',
  branch: '',
  notes: '',
  balance: 0,
})

// Modal state for adding a bank account
const showAddModal = ref(false)

function openAddModal() {
  // reset form and open modal
  form.value = { bank_name: '', account_number: '', account_name: '', branch: '', notes: '', balance: 0 }
  showAddModal.value = true
}

function closeAddModal() {
  showAddModal.value = false
}

const accounts = computed(() => {
  if (!props.bankAccounts) return []
  return Array.isArray(props.bankAccounts) ? props.bankAccounts : (props.bankAccounts.data || [])
})

// Upcoming recurring items passed from backend (or empty)
const upcomingItems = computed(() => {
  return props.upcomingRecurring || []
})

function formatDate(d) {
  if (!d) return ''
  try {
    return new Date(d).toLocaleDateString()
  } catch (e) {
    return d
  }
}

function getAccountName(id) {
  const acc = accounts.value.find(a => a.id === id)
  return acc ? (acc.account_name || acc.bank_name) : null
}

// Chart setup
const balanceChart = ref(null)
let chartInstance = null

const accountLabels = computed(() => accounts.value.map(a => a.bank_name || `Acct ${a.id}`))
const accountBalances = computed(() => accounts.value.map(a => Number(a.balance || 0)))

function initChart() {
  if (!balanceChart.value) return
  const ctx = balanceChart.value.getContext('2d')
  // create a subtle vertical gradient for bars
  const grad = ctx.createLinearGradient(0, 0, 0, balanceChart.value.height)
  grad.addColorStop(0, 'rgba(124,58,237,0.95)')
  grad.addColorStop(1, 'rgba(251,191,36,0.65)')

  chartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: accountLabels.value,
      datasets: [
        {
          label: 'Balance',
          data: accountBalances.value,
          backgroundColor: accountBalances.value.map(() => grad),
          borderColor: accountBalances.value.map(() => 'rgba(79,70,229,1)'),
          borderWidth: 0,
          borderRadius: 6,
          barPercentage: 0.6,
          categoryPercentage: 0.7,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: {
          grid: { display: false },
        },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(15,23,42,0.06)' },
          ticks: {
            callback: function(value) {
              try {
                return new Intl.NumberFormat(undefined, { style: 'currency', currency: (props.auth && props.auth.user && props.auth.user.currency) ? props.auth.user.currency : 'USD' }).format(value)
              } catch (e) {
                return value
              }
            }
          }
        }
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: function(context) {
              const val = context.parsed.y ?? context.parsed ?? 0
              try {
                return new Intl.NumberFormat(undefined, { style: 'currency', currency: (props.auth && props.auth.user && props.auth.user.currency) ? props.auth.user.currency : 'USD' }).format(val)
              } catch (e) {
                return val
              }
            }
          }
        }
      },
      animation: { duration: 400 }
    },
  })
}

function updateChart() {
  if (!chartInstance) return initChart()
  chartInstance.data.labels = accountLabels.value
  chartInstance.data.datasets[0].data = accountBalances.value
  // update gradient in case canvas size changed
  const ctx = balanceChart.value.getContext('2d')
  const grad = ctx.createLinearGradient(0, 0, 0, balanceChart.value.height)
  grad.addColorStop(0, 'rgba(124,58,237,0.95)')
  grad.addColorStop(1, 'rgba(251,191,36,0.65)')
  chartInstance.data.datasets[0].backgroundColor = accountBalances.value.map(() => grad)
  chartInstance.update()
}

onMounted(() => {
  initChart()
})

watch([accountLabels, accountBalances], () => {
  updateChart()
})

function goToPage(url) {
  if (!url) return
  router.get(url, {}, { preserveState: true, preserveScroll: true })
}

// Modal state
const showModal = ref(false)
const selectedAccount = ref(null)
const editAccount = ref(null)

function handleAccountClick(account) {
  selectedAccount.value = account
  // Deep copy to avoid mutating the list directly
  editAccount.value = { ...account }
  showModal.value = true
}

function formatCurrency(amount) {
  const currency = (props.auth && props.auth.user && props.auth.user.currency) ? props.auth.user.currency : 'USD'
  try {
    return new Intl.NumberFormat(undefined, { style: 'currency', currency }).format(Number(amount || 0))
  } catch (e) {
    return Number(amount || 0).toFixed(2)
  }
}

function saveEdit() {
  if (!editAccount.value || !editAccount.value.id) return

  router.put(route('bank-accounts.update', editAccount.value.id), editAccount.value, {
    preserveScroll: true,
    onSuccess: () => {
      closeModal()
    },
    onFinish: () => {
      // ensure modal is closed after request completes
      closeModal()
    },
    onError: (errors) => {
      // optionally surface validation errors later
      console.error('Validation errors updating bank account:', errors)
    },
  })
}

function confirmDelete() {
  if (!editAccount.value || !editAccount.value.id) return
  if (!confirm('Are you sure you want to delete this bank account? This action cannot be undone.')) return

  // close modal immediately for a snappier UX, then perform delete
  closeModal()

  router.delete(route('bank-accounts.destroy', editAccount.value.id), {}, {
    preserveScroll: true,
    onError: (errors) => {
      console.error('Error deleting bank account:', errors)
    },
  })
}
function closeModal() {
  showModal.value = false
  selectedAccount.value = null
  editAccount.value = null
}

function submitBankAccount() {
  router.post(route('bank-accounts.store'), form.value, {
    preserveScroll: true,
    onSuccess: () => {
      form.value = { bank_name: '', account_number: '', account_name: '', branch: '', notes: '', balance: 0 }
      closeAddModal()
    },
  })
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
@keyframes popup {
  0% { transform: scale(0.95); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}
.animate-popup {
  animation: popup 0.2s cubic-bezier(0.4,0,0.2,1);
}
</style>
