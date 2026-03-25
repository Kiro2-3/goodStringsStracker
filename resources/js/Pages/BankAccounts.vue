<template>
  <AuthenticatedLayout :user="auth.user">
    <Head title="Bank Accounts" />
    <div class="min-h-screen w-full flex flex-col md:flex-row bg-base-200 text-base-content">
      <AppSidebar :user="auth.user" active-page="bank-accounts" />
      <main class="flex-1 min-w-0 px-4 md:px-12 py-8">
        <div class="max-w-5xl mx-auto flex flex-col md:flex-row gap-8">
          <!-- Add Bank Account Form -->
          <div class="w-full md:w-1/2">
            <div class="card bg-base-100 border border-base-200 shadow-xl p-8 h-full">
              <h2 class="text-2xl font-bold mb-6">Add Bank Account</h2>
              <form @submit.prevent="submitBankAccount">
                <div class="mb-4">
                  <label class="form-control w-full gap-2">
                    <span class="label-text font-semibold text-base-content">Bank Name</span>
                    <input v-model="form.bank_name" type="text" class="input input-bordered w-full bg-base-100 text-base-content" required />
                  </label>
                </div>
                <div class="mb-4">
                  <label class="form-control w-full gap-2">
                    <span class="label-text font-semibold text-base-content">Account Number</span>
                    <input v-model="form.account_number" type="text" class="input input-bordered w-full bg-base-100 text-base-content" required />
                  </label>
                </div>
                <div class="mb-4">
                  <label class="form-control w-full gap-2">
                    <span class="label-text font-semibold text-base-content">Account Name</span>
                    <input v-model="form.account_name" type="text" class="input input-bordered w-full bg-base-100 text-base-content" required />
                  </label>
                </div>
                <div class="mb-4">
                  <label class="form-control w-full gap-2">
                    <span class="label-text font-semibold text-base-content">Bank Branch</span>
                    <input v-model="form.branch" type="text" class="input input-bordered w-full bg-base-100 text-base-content" />
                  </label>
                </div>
                <div class="mb-6">
                  <label class="form-control w-full gap-2">
                    <span class="label-text font-semibold text-base-content">Notes</span>
                    <textarea v-model="form.notes" class="textarea textarea-bordered w-full bg-base-100 text-base-content" rows="2"></textarea>
                  </label>
                </div>
                <div class="mb-4">
                  <label class="form-control w-full gap-2">
                    <span class="label-text font-semibold text-base-content">Current Saving</span>
                    <input v-model.number="form.balance" type="number" step="0.01" class="input input-bordered w-full bg-base-100 text-base-content" />
                  </label>
                </div>
                <button type="submit" class="btn btn-primary w-full">Save Bank Account</button>
              </form>
            </div>
          </div>

          <!-- Saved Bank Accounts Card -->
          <div class="w-full md:w-1/2 flex flex-col">
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

            <div v-if="accounts && accounts.length" class="card bg-base-100 border border-base-200 shadow p-6 h-full">
              <h3 class="text-xl font-semibold mb-4">Saved Bank Accounts</h3>
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
            <div v-else class="card bg-base-100 border border-base-200 shadow p-6 text-center text-base-content/60 h-full flex items-center justify-center">
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
        </div>
      </main>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import AppSidebar from '@/Components/AppSidebar.vue'

const props = defineProps({
  auth: Object,
  bankAccounts: Object,
  totalBalance: Number,
})

const form = ref({
  bank_name: '',
  account_number: '',
  account_name: '',
  branch: '',
  notes: '',
  balance: 0,
})

const accounts = computed(() => {
  if (!props.bankAccounts) return []
  return Array.isArray(props.bankAccounts) ? props.bankAccounts : (props.bankAccounts.data || [])
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

  router.delete(route('bank-accounts.destroy', editAccount.value.id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      closeModal()
    },
    onFinish: () => {
      // ensure modal is closed after request completes
      closeModal()
    },
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
