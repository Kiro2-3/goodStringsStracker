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
        v-if="flash.success && showFlash"
        class="toast toast-top toast-end z-50"
      >
        <div class="alert alert-success flex items-center gap-2 bg-green-500 text-white">
          <span class="text-white">{{ flash.success }}</span>
          <button
            type="button"
            class="btn btn-xs btn-ghost text-white/80 hover:text-white ml-2"
            @click="showFlash = false"
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

    <!-- Notification bell: fixed top-right on desktop, hidden on mobile (shown in mobile navbar) -->
    <div class="hidden md:block fixed top-4 right-4 z-50">
      <NotificationPopover />
    </div>

    <main><slot /></main>
  </div>
</template>

<script setup>
import { computed, onUnmounted, ref, watch } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
import ThemeToggle from '@/Components/ThemeToggle.vue'
import NotificationPopover from '@/Components/NotificationPopover.vue'

const props = defineProps({
  header: String
})

// Access reactive Inertia page props shared on every server response
const page   = usePage()
const user   = computed(() => page.props.auth.user)
const flash  = computed(() => page.props.flash || {})  // server flash messages (success, error, etc.)
const showingNavigationDropdown = ref(false)
const showFlash                 = ref(false)
let flashTimeout = null  // holds the auto-hide timer so it can be cleared on component unmount

/**
 * Watches the flash prop for new success messages.
 * A brief delay before showing prevents the transition from skipping
 * when Inertia replaces props immediately after a redirect.
 * The toast auto-dismisses after 3 seconds.
 */
watch(
  () => page.props.flash,
  (flashObj) => {
    if (flashObj?.success) {
      showFlash.value = false
      if (flashTimeout) clearTimeout(flashTimeout)
      setTimeout(() => {
        showFlash.value = true
        flashTimeout = setTimeout(() => {
          showFlash.value = false
        }, 3000)
      }, 10)
    }
  },
  { immediate: true },
)

// Clean up the timer to prevent state updates after the component is destroyed
onUnmounted(() => {
  if (flashTimeout) clearTimeout(flashTimeout)
})
</script>

<style scoped>
</style>
