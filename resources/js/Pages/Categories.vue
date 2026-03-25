<template>
  <AuthenticatedLayout :user="auth.user">
    <Head title="Categories" />

    <div class="min-h-screen w-full flex flex-col md:flex-row bg-base-200 text-base-content">
      <!-- Sidebar -->
      <AppSidebar
        :user="auth.user"
        active-page="categories"
        @add-transaction="showAddTransaction = true"
      />

      <!-- Main content -->
      <main class="flex-1 min-w-0 px-4 md:px-12 py-8">
        <div class="card bg-base-100 border border-base-200 shadow-xl overflow-hidden">
          <!-- Header -->
          <div class="p-6 border-b border-base-200 flex items-center justify-between">
            <div>
              <h1 class="text-xl font-bold text-base-content">Categories</h1>
              <p class="text-sm text-base-content/60 mt-0.5">Manage the expense categories used in your transactions.</p>
            </div>
            <button
              class="btn btn-primary btn-sm gap-2"
              @click="openAddModal"
            >
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
              </svg>
              Add Category
            </button>
          </div>

          <!-- Filters -->
          <div class="card-body gap-4 border-b border-base-200">
            <div class="flex flex-col gap-4 md:flex-row md:items-end">
              <label class="form-control w-full md:flex-1 gap-2">
                <span class="label-text font-semibold text-base-content">Search</span>
                <input
                  id="filter-search"
                  type="text"
                  v-model="filters.search"
                  placeholder="Search category name"
                  class="input input-bordered w-full bg-base-100 text-base-content"
                />
              </label>

              <label class="form-control w-full md:w-40 gap-2">
                <span class="label-text font-semibold text-base-content">Sort</span>
                <select id="filter-sort-dir" v-model="filters.sort_dir" class="select select-bordered w-full bg-base-100 text-base-content">
                  <option value="asc">Name ↑</option>
                  <option value="desc">Name ↓</option>
                </select>
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
            </div>
          </div>

          <!-- Debug (enable by adding ?debug=1 to the URL) -->
          <div v-if="showDebug" class="p-4 bg-base-100 border-b border-base-200 text-xs">
            <strong class="mb-2 block">Debug: categories prop / total_count</strong>
            <pre class="whitespace-pre-wrap text-sm">{{ debugData }}</pre>
          </div>

          <!-- Table -->
          <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th class="text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(cat, index) in localCategories"
                  :key="cat.id"
                >
                  <td class="text-base-content/50 text-sm">{{ index + 1 }}</td>
                  <td :class="['font-medium', cat.name === 'Salary' ? 'text-green-600' : 'text-base-content']">{{ cat.name }}</td>
                  <td class="text-right">
                    <template v-if="cat.name !== 'Salary'">
                      <button
                        class="btn btn-ghost btn-xs text-primary mr-1"
                        @click="openEditModal(cat)"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 3.487a2.25 2.25 0 013.182 3.182L7.5 19.213l-4.5 1.125 1.125-4.5L16.862 3.487z" />
                        </svg>
                        Edit
                      </button>
                      <button
                        class="btn btn-ghost btn-xs text-error"
                        @click="confirmDelete(cat)"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                      </button>
                    </template>
                    <span v-else class="text-xs font-semibold text-green-600">Default</span>
                  </td>
                </tr>
                <tr v-if="localCategories.length === 0">
                  <td colspan="3" class="p-10 text-center text-base-content/50">No categories yet. Add one above.</td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- Pagination -->
          <div v-if="hasMultiplePages" class="flex items-center justify-between px-4 py-3 border-t border-base-200">
            <span class="text-sm text-base-content/60">
              Page {{ displayCurrentPage }} of {{ displayLastPage }}
              &nbsp;·&nbsp; {{ displayTotal }} total
            </span>
            <div class="flex gap-2">
              <button class="btn btn-sm btn-outline" :disabled="displayCurrentPage <= 1" @click="goToPage(displayCurrentPage - 1)">← Prev</button>
              <button class="btn btn-sm btn-primary" :disabled="displayCurrentPage >= displayLastPage" @click="goToPage(displayCurrentPage + 1)">Next →</button>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Add Category Modal -->
    <div
      v-if="showAddModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4"
      @click.self="closeAddModal"
    >
      <div class="card bg-base-100 shadow-2xl w-full max-w-sm relative">
        <div class="card-body gap-4">
          <button
            type="button"
            class="btn btn-circle btn-sm btn-error absolute -right-3 -top-3 shadow"
            @click="closeAddModal"
          >✕</button>

          <h2 class="card-title text-base-content">Add Category</h2>

          <label class="form-control w-full gap-1">
            <span class="label-text font-semibold text-base-content">Category name</span>
            <input
              id="cat-name"
              type="text"
              v-model="newCategoryName"
              placeholder="e.g. Groceries"
              class="input input-bordered w-full bg-base-100 text-base-content"
              @keydown.enter.prevent="saveCategory"
            />
            <span v-if="addError" class="label-text-alt text-error">{{ addError }}</span>
          </label>

          <div class="card-actions justify-end">
            <button type="button" class="btn btn-ghost btn-sm" @click="closeAddModal">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm" :disabled="saving" @click="saveCategory">
              {{ saving ? 'Saving…' : 'Save' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Category Modal -->
    <div
      v-if="showEditModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4"
      @click.self="closeEditModal"
    >
      <div class="card bg-base-100 shadow-2xl w-full max-w-sm relative">
        <div class="card-body gap-4">
          <button
            type="button"
            class="btn btn-circle btn-sm btn-error absolute -right-3 -top-3 shadow"
            @click="closeEditModal"
          >✕</button>

          <h2 class="card-title text-base-content">Edit Category</h2>

          <label class="form-control w-full gap-1">
            <span class="label-text font-semibold text-base-content">Category name</span>
            <input
              id="edit-cat-name"
              type="text"
              v-model="editCategoryName"
              placeholder="e.g. Groceries"
              class="input input-bordered w-full bg-base-100 text-base-content"
              @keydown.enter.prevent="saveEdit"
            />
            <span v-if="editError" class="label-text-alt text-error">{{ editError }}</span>
          </label>

          <div class="card-actions justify-end">
            <button type="button" class="btn btn-ghost btn-sm" @click="closeEditModal">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm" :disabled="editing" @click="saveEdit">
              {{ editing ? 'Saving…' : 'Save' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="categoryToDelete"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4"
      @click.self="categoryToDelete = null"
    >
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 relative">
        <h2 class="text-lg font-bold text-gray-800 mb-2">Delete Category</h2>
        <p class="text-sm text-gray-600 mb-6">
          Are you sure you want to delete <span class="font-semibold text-gray-900">{{ categoryToDelete.name }}</span>?
          Existing transactions using this category will keep it as a label.
        </p>
        <div class="flex justify-end gap-2">
          <button type="button" class="btn btn-ghost btn-sm" @click="categoryToDelete = null">Cancel</button>
          <button type="button" class="btn btn-error btn-sm text-white" @click="deleteCategory">Delete</button>
        </div>
      </div>
    </div>
    <!-- AddTransaction Modal -->
    <AddTransaction
      v-if="showAddTransaction"
      :categories="localCategories.map(c => c.name)"
      @close="showAddTransaction = false"
    />
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import axios from 'axios'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import AppSidebar from '@/Components/AppSidebar.vue'
import AddTransaction from '@/Pages/AddTransaction.vue'

const props = defineProps({
  auth:       { type: Object, required: true },
  // categories can be either an Array (legacy) or a paginated object { data, meta, links }
  categories: { type: [Array, Object],  default: () => [] },
  flash:      { type: Object, default: () => ({}) },
  filters:    { type: Object, default: () => ({}) },
  total_count: { type: Number, default: 0 },
})

const categoriesProp = props.categories

// Local copy of the categories list; if server provides a paginator use its `data` page
const localCategories = ref(props.categories?.data ? [...props.categories.data] : [...(props.categories || [])])

// keep localCategories in sync when server sends new data (pagination/search)
watch(
  () => props.categories,
  (newVal) => {
    localCategories.value = newVal?.data ? [...newVal.data] : [...(newVal || [])]
  },
  { immediate: true }
)
const showAddTransaction = ref(false)

// ─── Add Category ─────────────────────────────────────────────────────────────
const showAddModal    = ref(false)
const newCategoryName = ref('')
const addError        = ref('')
const saving          = ref(false)   // disables the Save button while the axios request is in-flight

function openAddModal() {
  newCategoryName.value = ''
  addError.value = ''
  showAddModal.value = true
}

function closeAddModal() {
  showAddModal.value = false
}

/**
 * Validates uniqueness (case-insensitive) before POSTing to the server.
 * On success, inserts the new category into the local list and re-sorts alphabetically.
 */
function saveCategory() {
  const name = newCategoryName.value.trim()

  if (!name) {
    addError.value = 'Category name is required.'
    return
  }

  if (localCategories.value.some((c) => c.name.toLowerCase() === name.toLowerCase())) {
    addError.value = 'This category already exists.'
    return
  }

  saving.value   = true
  addError.value = ''

  axios.post(route('categories.store'), { name })
    .then(({ data }) => {
      localCategories.value.push({ id: data.id, name: data.name })
      localCategories.value.sort((a, b) => a.name.localeCompare(b.name))
      showAddModal.value = false
    })
    .catch((err) => {
      addError.value = err.response?.data?.errors?.name?.[0] ?? 'Unable to save category.'
    })
    .finally(() => {
      saving.value = false
    })
}

// ─── Edit Category ────────────────────────────────────────────────────────────
const showEditModal    = ref(false)
const categoryToEdit   = ref(null)   // the category object currently being edited
const editCategoryName = ref('')
const editError        = ref('')
const editing          = ref(false)

function openEditModal(cat) {
  categoryToEdit.value   = cat
  editCategoryName.value = cat.name
  editError.value        = ''
  showEditModal.value    = true
}

function closeEditModal() {
  showEditModal.value  = false
  categoryToEdit.value = null
}

/**
 * Validates uniqueness (excluding the current category being edited) before
 * sending a PUT request; mutates the local list entry on success.
 */
function saveEdit() {
  const name = editCategoryName.value.trim()

  if (!name) {
    editError.value = 'Category name is required.'
    return
  }

  if (
    localCategories.value.some(
      (c) => c.id !== categoryToEdit.value.id && c.name.toLowerCase() === name.toLowerCase(),
    )
  ) {
    editError.value = 'A category with this name already exists.'
    return
  }

  editing.value   = true
  editError.value = ''

  axios.put(route('categories.update', categoryToEdit.value.id), { name })
    .then(({ data }) => {
      const cat = localCategories.value.find((c) => c.id === data.id)
      if (cat) {
        cat.name = data.name
      }
      localCategories.value.sort((a, b) => a.name.localeCompare(b.name))
      showEditModal.value = false
    })
    .catch((err) => {
      editError.value = err.response?.data?.errors?.name?.[0] ?? 'Unable to update category.'
    })
    .finally(() => {
      editing.value = false
    })
}

// ─── Delete Category ──────────────────────────────────────────────────────────
const categoryToDelete = ref(null)   // set to the target category to open the confirm modal

function confirmDelete(cat) {
  categoryToDelete.value = cat
}

/**
 * Deletes the confirmed category via Inertia DELETE.
 * Existing transactions keep the category string as a plain label even after deletion.
 */
function deleteCategory() {
  const cat = categoryToDelete.value
  if (!cat) {
    return
  }

  categoryToDelete.value = null  // close the confirmation modal before the request

  router.delete(route('categories.destroy', cat.id), {
    preserveScroll: true,
    onSuccess: () => {
      // after delete, remove from local list if present
      localCategories.value = localCategories.value.filter(c => c.id !== cat.id)
    }
  })
}

// --- Filters (server-driven) -------------------------------------------------
const filters = ref({
  search:   props.filters?.search || '',
  sort_by:  props.filters?.sort_by || 'name',
  sort_dir: props.filters?.sort_dir || 'asc',
})

function applyFilters() {
  router.get(route('categories.index'), filters.value, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

function goToPage(page) {
  const params = { ...filters.value, page }
  router.get(route('categories.index'), params, {
    // don't preserve component state so server paginator metadata replaces props
    preserveState: false,
    preserveScroll: true,
  })
}

// Fallback helpers for pagination when the server doesn't send paginator metadata
const currentPage = computed(() => {
  const qs = new URLSearchParams(window.location.search)
  const p = Number(qs.get('page'))
  return Number.isInteger(p) && p > 0 ? p : 1
})

const hasMultiplePages = computed(() => {
  if (categoriesProp && categoriesProp.meta) {
    return categoriesProp.meta.last_page > 1
  }
  // fallback using server-provided absolute count if available
  const totalCount = props.total_count || 0
  if (totalCount && totalCount > 10) {
    return true
  }

  // final fallback: if the current visible list already has >= 10 items
  return Array.isArray(localCategories.value) && localCategories.value.length >= 10
})

const showDebug = computed(() => new URLSearchParams(window.location.search).get('debug') === '1')

const debugData = computed(() => JSON.stringify({ categories: categoriesProp, total_count: totalCount }, null, 2))

const displayCurrentPage = computed(() => {
  return categoriesProp?.meta?.current_page ?? currentPage.value
})

const displayLastPage = computed(() => {
  if (categoriesProp?.meta?.last_page) return categoriesProp.meta.last_page
  if (props.total_count && props.total_count > 0) return Math.max(1, Math.ceil(props.total_count / 10))
  return Math.max(1, Math.ceil((localCategories.value?.length || 0) / 10))
})

const displayTotal = computed(() => {
  if (categoriesProp?.meta?.total) return categoriesProp.meta.total
  if (props.total_count) return props.total_count
  return localCategories.value?.length || 0
})
function clearFilters() {
  filters.value = { search: '', sort_by: 'name', sort_dir: 'asc' }
  router.get(route('categories.index'), {}, { preserveState: true, replace: true })
}
</script>
