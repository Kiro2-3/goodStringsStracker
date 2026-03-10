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
    <main><slot /></main>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
// DropdownTrigger, DropdownContent, DropdownLink are subcomponents of Dropdown.vue
import DropdownTrigger from '@/Components/Dropdown.vue';
import DropdownContent from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const props = defineProps({
  header: String
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const flash = computed(() => page.props.flash || {});
const showingNavigationDropdown = ref(false);
const showFlash = ref(false);

let flashTimeout = null;

watch(
  () => page.props.flash,
  (newFlash) => {
    if (newFlash && newFlash.success) {
      showFlash.value = true;

      if (flashTimeout) {
        clearTimeout(flashTimeout);
      }

      flashTimeout = setTimeout(() => {
        showFlash.value = false;
      }, 3000);
    }
  },
  { deep: false }
);
</script>

<style scoped>
</style>
