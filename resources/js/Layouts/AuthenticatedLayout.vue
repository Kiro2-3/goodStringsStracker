<template>
  <div class="min-h-screen bg-base-200 text-base-content dark:bg-base-300 dark:text-base-100 relative">
    <transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="opacity-0 translate-y-2 scale-95"
      enter-to-class="opacity-100 translate-y-0 scale-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100 translate-y-0 scale-100"
      leave-to-class="opacity-0 -translate-y-1 scale-95"
    >
      <div
        v-if="currentToast"
        :key="currentToast.id"
        class="toast toast-top toast-end z-[9999]"
      >
        <div
          :class="[
            'alert flex items-center gap-2 text-white shadow-lg min-w-[260px]',
            currentToast.type === 'success' ? 'bg-green-500' : 'bg-red-500',
          ]"
        >
          <svg v-if="currentToast.type === 'success'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
          </svg>
          <span class="flex-1 text-sm font-medium">{{ currentToast.message }}</span>
          <button
            type="button"
            class="btn btn-xs btn-ghost text-white/80 hover:text-white ml-1"
            @click="currentToast = null"
          >
            ✕
          </button>
        </div>
      </div>
    </transition>
    <header v-if="header" class="bg-base-100 dark:bg-base-200 shadow">
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <slot name="header">{{ header }}</slot>
      </div>
    </header>

    <main><slot /></main>
    <!-- Floating mascot helper -->
    <div class="mascot-wrapper" v-cloak>
      <div class="mascot-badge" @click="toggleMascot" role="button" aria-label="Open help">Do you need help?</div>
      <div class="mascot-button" @click="toggleMascot" role="button" aria-label="Open help">
        <img src="/public/images/mascot1.png" alt="Mascot" class="mascot-img" />
      </div>
      <transition name="fade">
        <div v-if="showMascot" class="mascot-bubble">
          <ChatBot />
        </div>
      </transition>
    </div>
  </div>
</template>

<script setup>
import { computed, nextTick, onUnmounted, ref, watch, onMounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
import ThemeToggle from '@/Components/ThemeToggle.vue'
import ChatBot from '@/Components/ChatBot.vue'
import { onBeforeUnmount } from 'vue'

const props = defineProps({
  header: String
})

// Access reactive Inertia page props shared on every server response
const page   = usePage()
const user   = computed(() => page.props.auth.user)
const flash  = computed(() => page.props.flash || {})  // server flash messages (success, error, etc.)
const showingNavigationDropdown = ref(false)

// Unified toast state — a single active toast object or null when hidden
let toastCounter = 0
const currentToast = ref(null)
let flashTimeout = null

/**
 * Watches the actual flash message strings (not the container object) so it
 * fires reliably even when Inertia reuses the same flash object reference.
 * Handles both success and error flash keys.
 * Uses nextTick to reset the toast before showing the new one so that the
 * enter-transition always replays, even for identical consecutive messages.
 */
watch(
  () => [page.props.flash?.success, page.props.flash?.error],
  async ([success, error]) => {
    const message = success || error
    const type    = success ? 'success' : error ? 'error' : null
    if (!message) return

    if (flashTimeout) clearTimeout(flashTimeout)
    currentToast.value = null       // clear first so transition re-enters
    await nextTick()
    currentToast.value = { id: ++toastCounter, type, message }
    flashTimeout = setTimeout(() => { currentToast.value = null }, 4000)
  },
  { immediate: true },
)

// Clean up the timer to prevent state updates after the component is destroyed
onUnmounted(() => {
  if (flashTimeout) clearTimeout(flashTimeout)
})

// Initialize theme early on layout mount to avoid FOUC between light/dark
onMounted(() => {
  try {
    const stored = localStorage.getItem('theme')
    if (stored === 'dark') {
      document.documentElement.classList.add('dark')
      try { document.documentElement.setAttribute('data-theme', 'dark') } catch (e) {}
      try { document.body.classList.remove('bg-base-200','bg-base-100'); document.body.classList.add('bg-base-300') } catch (e) {}
    } else if (stored === 'light') {
      document.documentElement.classList.remove('dark')
      try { document.documentElement.setAttribute('data-theme', 'purplegold') } catch (e) {}
      try { document.body.classList.remove('bg-base-300','bg-base-100'); document.body.classList.add('bg-base-200') } catch (e) {}
    } else {
      // no stored preference: respect system preference
      if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.classList.add('dark')
        try { document.documentElement.setAttribute('data-theme', 'dark') } catch (e) {}
        try { document.body.classList.remove('bg-base-200','bg-base-100'); document.body.classList.add('bg-base-300') } catch (e) {}
      } else {
        document.documentElement.classList.remove('dark')
        try { document.documentElement.setAttribute('data-theme', 'purplegold') } catch (e) {}
        try { document.body.classList.remove('bg-base-300','bg-base-100'); document.body.classList.add('bg-base-200') } catch (e) {}
      }
    }
  } catch (e) {
    // ignore
  }
})

// Mascot helper state and handlers
const showMascot = ref(false)
function toggleMascot() {
  showMascot.value = !showMascot.value
}

function onKeydown(e) {
  if (e.key === 'Escape') showMascot.value = false
}

window.addEventListener('keydown', onKeydown)
onBeforeUnmount(() => {
  window.removeEventListener('keydown', onKeydown)
})
</script>

<style scoped>
/* Mascot floating helper styles */
.mascot-wrapper {
  position: fixed;
  right: 1rem;
  bottom: 1.25rem;
  z-index: 60;
}
.mascot-button {
  width: 56px;
  height: 56px;
  border-radius: 9999px;
  overflow: hidden;
  box-shadow: 0 6px 20px rgba(0,0,0,0.12);
  cursor: pointer;
}
.mascot-img { width: 100%; height: 100%; object-fit: cover; display: block }
.mascot-bubble {
  margin-bottom: 0.5rem;
  transform: translateY(-8px);
  padding: 0; /* let ChatBot control padding */
  border-radius: 12px;
  box-shadow: 0 12px 40px rgba(2,6,23,0.18);
  display: block;
  max-width: 92vw;
}
.fade-enter-active, .fade-leave-active { transition: opacity 0.15s ease, transform 0.15s ease }
.fade-enter-from { opacity: 0; transform: translateY(6px) }
.fade-enter-to { opacity: 1; transform: translateY(0) }
.fade-leave-from { opacity: 1; transform: translateY(0) }
.fade-leave-to { opacity: 0; transform: translateY(6px) }

/* Persistent badge next to mascot */
.mascot-badge {
  position: absolute;
  right: 74px;
  bottom: 10px;
  background: linear-gradient(90deg,#7c3aed,#f59e0b);
  color: #fff;
  padding: 0.32rem 0.6rem;
  border-radius: 9999px;
  font-weight: 600;
  font-size: 0.82rem;
  box-shadow: 0 8px 24px rgba(0,0,0,0.12);
  cursor: pointer;
  white-space: nowrap;
  transform-origin: right center;
}
.mascot-badge:hover { transform: translateY(-2px) }

@media (max-width: 480px) {
  .mascot-badge { right: 64px; font-size: 0.72rem; padding: 0.26rem 0.5rem }
}

@media (min-width: 768px) {
  .mascot-wrapper { right: 1.5rem; bottom: 1.5rem }
}

</style>
 
