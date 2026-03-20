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
        v-if="unreadCount > 0"
        class="absolute -top-2 left-full -translate-x-1/2 inline-flex items-center justify-center rounded-full bg-primary text-primary-content text-[10px] font-bold h-5 min-w-[20px] px-1 leading-none"
      >
        {{ unreadCount > 99 ? '99+' : unreadCount }}
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
        <!-- Header: Tabs + Mark all as read -->
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-base-300 px-3 py-2">
          <div class="flex gap-0.5 bg-gray-100 dark:bg-base-300 rounded-lg p-0.5">
            <button
              :class="[
                'inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium transition-all',
                tab === 'all'
                  ? 'bg-white text-gray-900 shadow-sm dark:bg-base-100 dark:text-base-content'
                  : 'text-gray-500 hover:text-gray-700 dark:text-base-content/50 dark:hover:text-base-content/70'
              ]"
              @click="tab = 'all'"
            >All</button>
            <button
              :class="[
                'inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium transition-all',
                tab === 'unread'
                  ? 'bg-white text-gray-900 shadow-sm dark:bg-base-100 dark:text-base-content'
                  : 'text-gray-500 hover:text-gray-700 dark:text-base-content/50 dark:hover:text-base-content/70'
              ]"
              @click="tab = 'unread'"
            >
              Unread
              <span
                v-if="unreadCount > 0"
                class="inline-flex items-center justify-center rounded-full bg-primary text-primary-content text-[10px] font-bold h-4 min-w-[16px] px-1 leading-none"
              >{{ unreadCount }}</span>
            </button>
          </div>
          <button
            v-if="unreadCount > 0"
            class="text-xs font-medium text-gray-400 hover:text-gray-600 hover:underline dark:text-base-content/40 dark:hover:text-base-content/70"
            @click="markAllAsRead"
          >Mark all as read</button>
        </div>

        <!-- Notification list -->
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-base-300">
          <div v-if="filtered.length === 0" class="px-3 py-6 text-center text-sm text-gray-400 dark:text-base-content/40">
            No notifications
          </div>
          <button
            v-for="n in filtered"
            :key="n.id"
            class="flex w-full items-start gap-3 px-3 py-3 text-left hover:bg-gray-50 transition-colors dark:hover:bg-base-300"
            @click="markAsRead(n.id)"
          >
            <!-- Icon -->
            <div class="mt-0.5 text-gray-400 shrink-0 dark:text-base-content/40">
              <!-- git-merge -->
              <svg v-if="n.iconType === 'git-merge'" xmlns="http://www.w3.org/2000/svg" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="18" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><path d="M6 21V9a9 9 0 0 0 9 9"/>
              </svg>
              <!-- file-text -->
              <svg v-else-if="n.iconType === 'file-text'" xmlns="http://www.w3.org/2000/svg" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/>
              </svg>
              <!-- clipboard-check -->
              <svg v-else-if="n.iconType === 'clipboard-check'" xmlns="http://www.w3.org/2000/svg" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/>
              </svg>
              <!-- mail -->
              <svg v-else-if="n.iconType === 'mail'" xmlns="http://www.w3.org/2000/svg" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
              </svg>
              <!-- message-quote -->
              <svg v-else-if="n.iconType === 'message-quote'" xmlns="http://www.w3.org/2000/svg" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M8 10h.01"/><path d="M12 10h.01"/><path d="M16 10h.01"/>
              </svg>
              <!-- alert-circle -->
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
              </svg>
            </div>

            <!-- Content -->
            <div class="flex-1 space-y-0.5 min-w-0">
              <p :class="['text-sm leading-snug', n.unread ? 'font-semibold text-gray-900 dark:text-base-content' : 'text-gray-600 dark:text-base-content/70']">
                {{ n.user }} {{ n.action }} <span class="font-medium">{{ n.target }}</span>
              </p>
              <p class="text-xs text-gray-400 dark:text-base-content/40">{{ n.timestamp }}</p>
            </div>

            <!-- Unread dot -->
            <span v-if="n.unread" class="mt-1.5 inline-block h-2 w-2 rounded-full bg-primary shrink-0" />
          </button>
        </div>

        <!-- Footer -->
        <div class="border-t border-gray-100 dark:border-base-300 px-3 py-2">
          <button class="w-full rounded-lg px-3 py-1.5 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors dark:text-base-content/50 dark:hover:bg-base-300 dark:hover:text-base-content/70">
            View all notifications
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const open = ref(false)
const tab = ref('all')
const containerRef = ref(null)

const notifications = ref([
  {
    id: 1,
    user: 'Alicia Keys',
    action: 'merged',
    target: 'PR #105: Dark mode support',
    timestamp: '10 minutes ago',
    unread: true,
    iconType: 'git-merge',
  },
  {
    id: 2,
    user: 'Daniel Green',
    action: 'shared file',
    target: 'Quarterly Report.pdf',
    timestamp: '30 minutes ago',
    unread: true,
    iconType: 'file-text',
  },
  {
    id: 3,
    user: 'Sophia Turner',
    action: 'assigned you a task',
    target: 'Marketing campaign brief',
    timestamp: '2 hours ago',
    unread: false,
    iconType: 'clipboard-check',
  },
  {
    id: 4,
    user: 'Michael Ross',
    action: 'sent you a message',
    target: 'Project feedback discussion',
    timestamp: '5 hours ago',
    unread: false,
    iconType: 'mail',
  },
  {
    id: 5,
    user: 'Priya Sharma',
    action: 'added a comment',
    target: 'UX Review Notes',
    timestamp: '1 day ago',
    unread: false,
    iconType: 'message-quote',
  },
  {
    id: 6,
    user: 'System',
    action: 'alert',
    target: 'Server downtime scheduled',
    timestamp: '3 days ago',
    unread: false,
    iconType: 'alert-circle',
  },
])

const unreadCount = computed(() => notifications.value.filter((n) => n.unread).length)

const filtered = computed(() =>
  tab.value === 'unread'
    ? notifications.value.filter((n) => n.unread)
    : notifications.value,
)

function markAsRead(id) {
  notifications.value = notifications.value.map((n) =>
    n.id === id ? { ...n, unread: false } : n,
  )
}

function markAllAsRead() {
  notifications.value = notifications.value.map((n) => ({ ...n, unread: false }))
}

function handleClickOutside(event) {
  if (containerRef.value && !containerRef.value.contains(event.target)) {
    open.value = false
  }
}

onMounted(() => document.addEventListener('mousedown', handleClickOutside))
onUnmounted(() => document.removeEventListener('mousedown', handleClickOutside))
</script>
