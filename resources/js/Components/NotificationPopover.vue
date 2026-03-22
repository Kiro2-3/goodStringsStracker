<template>
  <div class="relative" ref="containerRef">
    <!-- Bell trigger button -->
    <button
      type="button"
      class="relative inline-flex items-center justify-center h-9 w-9 rounded-lg border border-gray-200 bg-white shadow-sm hover:bg-gray-50 transition-colors dark:bg-base-200 dark:border-base-300 dark:hover:bg-base-300"
      aria-label="Open notifications"
      @click="open = !open"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600 dark:text-base-content" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
        <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
      </svg>
      <span
        v-if="items.length > 0"
        class="absolute -top-2 left-full -translate-x-1/2 inline-flex items-center justify-center rounded-full bg-primary text-primary-content text-[10px] font-bold h-5 min-w-[20px] px-1 leading-none"
      >
        {{ items.length > 99 ? '99+' : items.length }}
      </span>
    </button>

    <!-- Popover panel -->
    <transition
      enter-active-class="transition ease-out duration-150 origin-top-right"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition ease-in duration-100 origin-top-right"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-show="open"
        class="absolute right-0 mt-2 w-[380px] max-w-[calc(100vw-2rem)] z-50 rounded-xl border border-gray-200 bg-white shadow-xl shadow-black/5 overflow-hidden dark:bg-base-200 dark:border-base-300"
      >
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-base-300 px-4 py-3">
          <p class="text-sm font-semibold text-gray-900 dark:text-base-content">Recent Transactions</p>
          <span class="text-xs text-gray-400 dark:text-base-content/40">Last 5</span>
        </div>

        <!-- Transaction list -->
        <div class="divide-y divide-gray-100 dark:divide-base-300">
          <div v-if="items.length === 0" class="px-4 py-6 text-center text-sm text-gray-400 dark:text-base-content/40">
            No transactions yet
          </div>
          <div
            v-for="tx in items"
            :key="tx.id"
            class="flex items-center gap-3 px-4 py-3"
          >
            <!-- Type icon -->
            <div
              :class="[
                'shrink-0 inline-flex items-center justify-center h-8 w-8 rounded-full',
                tx.type === 'income'
                  ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400'
                  : 'bg-red-100 text-red-500 dark:bg-red-900/30 dark:text-red-400'
              ]"
            >
              <svg v-if="tx.type === 'income'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 19V5"/><path d="m5 12 7-7 7 7"/>
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 5v14"/><path d="m19 12-7 7-7-7"/>
              </svg>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 dark:text-base-content truncate leading-snug">
                {{ tx.description }}
              </p>
              <p class="text-xs text-gray-400 dark:text-base-content/40 mt-0.5">
                {{ tx.category }} · {{ tx.entry_date }}
              </p>
            </div>

            <!-- Amount -->
            <span
              :class="[
                'text-sm font-semibold shrink-0',
                tx.type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400'
              ]"
            >
              {{ tx.type === 'income' ? '+' : '-' }}{{ formatAmount(tx.amount) }}
            </span>
          </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-gray-100 dark:border-base-300 px-3 py-2">
          <button
            class="w-full rounded-lg px-3 py-1.5 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors dark:text-base-content/50 dark:hover:bg-base-300 dark:hover:text-base-content/70"
            @click="goToTransactions"
          >
            View all transactions
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

const open         = ref(false)  // controls popover visibility
const containerRef = ref(null)   // ref to the wrapper div; used to detect outside clicks

// Access the shared Inertia page props to read recently added transactions
const page = usePage()

// recentTransactions is injected server-side via Inertia's shared props on every request
const items = computed(() => page.props.recentTransactions ?? [])

// Formats a numeric amount with locale-aware thousand separators and 2 decimal places
function formatAmount(amount) {
  return new Intl.NumberFormat(undefined, {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount)
}

// Closes the popover then navigates to the full transactions list page
function goToTransactions() {
  open.value = false
  router.get(route('transactions.recent'))
}

// Closes the popover when a click is detected outside the container element
function handleClickOutside(event) {
  if (containerRef.value && !containerRef.value.contains(event.target)) {
    open.value = false
  }
}

// Attach / remove the global click listener to avoid memory leaks
onMounted(() => document.addEventListener('mousedown', handleClickOutside))
onUnmounted(() => document.removeEventListener('mousedown', handleClickOutside))
</script>
