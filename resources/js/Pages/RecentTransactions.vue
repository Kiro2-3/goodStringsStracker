<template>
  <AuthenticatedLayout :user="auth.user">
    <Head title="Transactions" />
    <div class="min-h-screen w-full flex flex-col md:flex-row bg-base-200 text-base-content">
      <AppSidebar
        :user="auth.user"
        active-page="transactions"
        @add-transaction="showAddTransaction = true"
      />

      <main class="flex-1 min-w-0 space-y-10 px-4 md:px-12 py-8">
        <div class="card bg-base-100 border border-base-200 shadow-xl overflow-hidden">
          <!-- Filters -->
          <div class="card-body gap-4 border-b border-base-200">
            <div class="flex flex-col gap-4 md:flex-row md:items-end">
              <label class="form-control w-full md:flex-1 gap-2">
                <span class="label-text font-semibold text-base-content">Search</span>
                <input
                  id="filter-search"
                  type="text"
                  v-model="filters.search"
                  placeholder="Search description or category"
                  class="input input-bordered w-full bg-base-100 text-base-content"
                />
              </label>
              <label class="form-control w-full md:w-40 gap-2">
                <span class="label-text font-semibold text-base-content">Type</span>
                <select
                  id="filter-type"
                  v-model="filters.type"
                  class="select select-bordered w-full bg-base-100 text-base-content"
                >
                  <option value="">All</option>
                  <option value="income">Income</option>
                  <option value="expense">Expense</option>
                </select>
              </label>
              <label class="form-control w-full md:w-40 gap-2">
                <span class="label-text font-semibold text-base-content">Category</span>
                <select
                  id="filter-category"
                  v-model="filters.category"
                  class="select select-bordered w-full bg-base-100 text-base-content"
                >
                  <option value="">All</option>
                  <option
                    v-for="cat in categories"
                    :key="cat"
                    :value="cat"
                  >
                    {{ cat }}
                  </option>
                </select>
              </label>
              <label class="form-control w-full md:w-40 gap-2">
                <span class="label-text font-semibold text-base-content">From</span>
                <input
                  id="filter-date-from"
                  type="date"
                  v-model="filters.date_from"
                  class="input input-bordered w-full bg-base-100 text-base-content"
                />
              </label>
              <label class="form-control w-full md:w-40 gap-2">
                <span class="label-text font-semibold text-base-content">To</span>
                <input
                  id="filter-date-to"
                  type="date"
                  v-model="filters.date_to"
                  class="input input-bordered w-full bg-base-100 text-base-content"
                />
              </label>
              <div class="flex w-full flex-col gap-1 md:w-auto">
                <span class="invisible label-text">a</span>
                <button type="button" @click="applyFilters" class="btn btn-primary">
                  Search
                </button>
              </div>
              <div class="flex w-full flex-col gap-1 md:w-auto">
                <span class="invisible label-text">a</span>
                <button type="button" @click="clearFilters" class="btn btn-ghost">
                  Clear
                </button>
              </div>
              <div class="flex w-full flex-col gap-1 md:w-auto">
                <span class="invisible label-text">a</span>
                <button type="button" @click="exportCsv" class="btn btn-outline btn-success gap-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                  </svg>
                  Export CSV
                </button>
              </div>
              <div class="flex w-full flex-col gap-1 md:w-auto">
                <span class="invisible label-text">a</span>
                <button type="button" class="btn btn-error" :disabled="selectedIds.length === 0" @click="bulkDelete">
                  Delete Selected ({{ selectedIds.length }})
                </button>
              </div>
            </div>
          </div>

          <!-- Table -->
          <div class="overflow-x-auto max-h-[32rem] overflow-y-auto table-scroll">
            <table class="table table-zebra w-full">
              <thead>
                <tr>
                  <th class="w-12">
                    <input type="checkbox" class="checkbox" :checked="allSelected" @change="toggleSelectAll" />
                  </th>
                  <th
                    v-for="col in sortableColumns"
                    :key="col.key"
                    :class="['cursor-pointer select-none whitespace-nowrap', col.key === 'actions' ? 'text-right' : '']"
                    @click="col.key !== 'actions' && sortBy(col.key)"
                  >
                    <span v-if="col.key !== 'actions'" class="inline-flex items-center gap-1">
                      {{ col.label }}
                      <!-- active sort indicator -->
                      <span v-if="filters.sort_by === col.key" class="text-primary">
                        <svg v-if="filters.sort_dir === 'asc'" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                      </span>
                      <!-- inactive double-arrow indicator -->
                      <span v-else class="opacity-30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4M16 15l-4 4-4-4" />
                        </svg>
                      </span>
                    </span>
                    <span v-else>{{ col.label }}</span>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="t in transactions.data" :key="t.id">
                  <td class="w-12">
                    <input type="checkbox" class="checkbox" :value="t.id" v-model="selectedIds" />
                  </td>
                  <td class="whitespace-nowrap">{{ t.entry_date }}</td>
                  <td>{{ t.description }}</td>
                  <td class="whitespace-nowrap" :class="t.category === 'Salary' ? 'text-green-600 font-semibold' : ''">{{ t.category }}</td>
                  <td
                    class="font-semibold whitespace-nowrap"
                    :class="t.type === 'income' ? 'text-green-600' : 'text-error'"
                  >
                    {{ t.type === 'income' ? '+' : '-' }}₱{{ Number(t.amount).toFixed(2) }}
                  </td>
                  <td class="uppercase text-xs font-bold tracking-wide">
                    <span
                      class="badge border-none px-3 py-2 text-xs font-semibold"
                      :class="t.type === 'income' ? 'bg-green-600 text-white' : 'badge-error'"
                    >
                      {{ t.type }}
                    </span>
                  </td>
                  <td class="text-right">
                    <div class="flex justify-end gap-2">
                      <button
                        type="button"
                        @click="openEditTransaction(t)"
                        class="btn btn-ghost btn-xs text-primary px-2"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4"
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
                        type="button"
                        @click="deleteTransaction(t.id)"
                        class="btn btn-ghost btn-xs text-error px-2"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4"
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
                  <td colspan="6" class="p-10 text-center text-base-content/50">No transactions found yet.</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div
            v-if="transactions.meta && transactions.meta.last_page > 1"
            class="flex items-center justify-between px-4 py-3 border-t border-base-200"
          >
            <span class="text-sm text-base-content/60">
              Page {{ transactions.meta.current_page }} of {{ transactions.meta.last_page }}
              &nbsp;·&nbsp; {{ transactions.meta.total }} total
            </span>
            <div class="flex gap-2">
              <button
                class="btn btn-sm btn-outline"
                :disabled="!transactions.links.prev"
                @click="transactions.links.prev && router.get(transactions.links.prev)"
              >
                ← Prev
              </button>
              <button
                class="btn btn-sm btn-primary"
                :disabled="!transactions.links.next"
                @click="transactions.links.next && router.get(transactions.links.next)"
              >
                Next →
              </button>
            </div>
          </div>
        </div>

        <EditTransaction
          v-if="editTransaction"
          :transaction="editTransaction"
          :categories="categories"
          @close="editTransaction = null"
        />
        <AddTransaction
          v-if="showAddTransaction"
          :categories="categories"
          @close="showAddTransaction = false"
        />
      </main>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch, computed, nextTick } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import AppSidebar from '@/Components/AppSidebar.vue'
import EditTransaction from '@/Pages/EditTransaction.vue'
import AddTransaction from '@/Pages/AddTransaction.vue'

const props = defineProps({
  auth:         Object,
  transactions: Object,   // paginated resource: { data, meta, links }
  categories:   Array,
  filters:      Object,   // active filter values sent back from the server after a Search
})

// expose a local `transactions` binding for template/script convenience
const transactions = props.transactions

// visibleTransactions holds a local, reactive copy of the current page's data
// so we can optimistically remove rows immediately when the user deletes them.
const visibleTransactions = ref((transactions?.data) ? [...transactions.data] : [])

// Keep visibleTransactions in sync when the server sends new paginated data
watch(
  () => transactions?.data,
  (newData) => {
    visibleTransactions.value = newData ? [...newData] : []
  },
  { immediate: true }
)

// Local filter state mirrors the server-side filters so inputs stay reactive
const filters = ref({
  search:    props.filters?.search    || '',
  type:      props.filters?.type      || '',
  category:  props.filters?.category  || '',
  date_from: props.filters?.date_from || '',
  date_to:   props.filters?.date_to   || '',
  sort_by:   props.filters?.sort_by   || 'entry_date',
  sort_dir:  props.filters?.sort_dir  || 'desc',
})

// Keep local filters in sync when Inertia navigates and the server sends updated props
watch(
  () => props.filters,
  (newFilters) => {
    filters.value = {
      search:    newFilters?.search    || '',
      type:      newFilters?.type      || '',
      category:  newFilters?.category  || '',
      date_from: newFilters?.date_from || '',
      date_to:   newFilters?.date_to   || '',
      sort_by:   newFilters?.sort_by   || 'entry_date',
      sort_dir:  newFilters?.sort_dir  || 'desc',
    }
  },
  { immediate: true },
)

const editTransaction    = ref(null)   // holds the transaction object being edited; null hides the modal
const showAddTransaction = ref(false)

// Bulk selection state
const selectedIds = ref([])

const allSelected = computed(() => {
  const idsOnPage = (visibleTransactions.value || []).map(t => t.id)
  return idsOnPage.length > 0 && idsOnPage.every(id => selectedIds.value.includes(id))
})

function toggleSelectAll() {
  const idsOnPage = (visibleTransactions.value || []).map(t => t.id)
  if (allSelected.value) {
    // remove page ids from selection
    selectedIds.value = selectedIds.value.filter(id => !idsOnPage.includes(id))
  } else {
    // add any missing ids from page
    selectedIds.value = Array.from(new Set([...selectedIds.value, ...idsOnPage]))
  }
}

function bulkDelete() {
  if (selectedIds.value.length === 0) return
  if (!confirm(`Delete ${selectedIds.value.length} selected transaction(s)? This cannot be undone.`)) return

  // optimistic removal from visibleTransactions for snappy UI
  const idsToRemove = Array.from(selectedIds.value)
  visibleTransactions.value = visibleTransactions.value.filter(t => !idsToRemove.includes(t.id))
  selectedIds.value = []

  router.post(route('transactions.bulk-delete'), { ids: idsToRemove }, {
    preserveScroll: true,
    onError: () => {
      // if server fails, reload to restore proper state
      router.reload()
    },
  })
}

const sortableColumns = [
  { key: 'entry_date',  label: 'Date' },
  { key: 'description', label: 'Description' },
  { key: 'category',    label: 'Category' },
  { key: 'amount',      label: 'Amount' },
  { key: 'type',        label: 'Type' },
  { key: 'actions',     label: 'Actions' },
]

// Spread into a new object so the form inside EditTransaction gets its own copy
function openEditTransaction(transaction) {
  editTransaction.value = { ...transaction }
}

/**
 * Sorts by the given column. Toggles direction if already sorted by that column,
 * otherwise defaults to ascending. Immediately reloads the page.
 */
function sortBy(column) {
  const newDir = filters.value.sort_by === column && filters.value.sort_dir === 'asc' ? 'desc' : 'asc'
  filters.value.sort_by  = column
  filters.value.sort_dir = newDir
  router.get(route('transactions.recent'), filters.value, {
    preserveState:  false,
    preserveScroll: true,
    replace:        true,
  })
}

/**
 * Clears all filter inputs and reloads the page without any query params,
 * resetting to the default paginated view.
 */
function clearFilters() {
  filters.value = {
    search:    '',
    type:      '',
    category:  '',
    date_from: '',
    date_to:   '',
    sort_by:   'entry_date',
    sort_dir:  'desc',
  }

  router.get(route('transactions.recent'), {}, {
    preserveScroll: true,
    replace:        true,  // replace history entry so back button skips the cleared state
  })
}

/**
 * Sends the current filter values as query params.
 * preserveState keeps the sidebar and other reactive data intact during the request.
 */
function applyFilters() {
  router.get(route('transactions.recent'), filters.value, {
    preserveState:  false,
    preserveScroll: true,
    replace:        true,
  })
}

// Confirms before deleting; uses preserveScroll to keep the user's scroll position
function deleteTransaction(id) {
  if (confirm('Are you sure you want to delete this transaction?')) {
    // optimistic remove
    visibleTransactions.value = visibleTransactions.value.filter(t => t.id !== id)

    router.delete(route('transactions.destroy', id), {
      preserveScroll: true,
      onError: () => {
        // restore from server if delete failed
        router.reload()
      },
    })
  }
}

/**
 * Builds a CSV download URL from the active filter state and triggers a full
 * page navigation (not Inertia) so the browser receives the file download response.
 */
function exportCsv() {
  const params = new URLSearchParams()

  if (filters.value.search)    params.set('search',    filters.value.search)
  if (filters.value.type)      params.set('type',      filters.value.type)
  if (filters.value.category)  params.set('category',  filters.value.category)
  if (filters.value.date_from) params.set('date_from', filters.value.date_from)
  if (filters.value.date_to)   params.set('date_to',   filters.value.date_to)

  const query = params.toString()
  window.location.href = route('transactions.export-csv') + (query ? '?' + query : '')
}
</script>

<style scoped>
.table-scroll::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
.table-scroll::-webkit-scrollbar-track {
  background: transparent;
}
.table-scroll::-webkit-scrollbar-thumb {
  background-color: #d1d5db;
  border-radius: 9999px;
}
.table-scroll::-webkit-scrollbar-thumb:hover {
  background-color: #9ca3af;
}
</style>
