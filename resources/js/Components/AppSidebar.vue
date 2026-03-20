<template>
  <!-- Mobile top navbar -->
  <div class="md:hidden fixed top-0 left-0 right-0 z-30 bg-base-100 border-b border-base-200 shadow-sm flex items-center justify-between px-4 py-3">
    <div class="flex items-center gap-2">
      <img src="/public/images/stracker-logo.png" alt="Stracker Logo" class="h-7 w-auto" />
      <span class="font-bold text-base tracking-tight text-base-content">Stracker</span>
    </div>
    <NotificationPopover class="md:hidden" />
    <button type="button" class="btn btn-ghost btn-sm p-1" @click="mobileOpen = !mobileOpen" aria-label="Toggle menu">
      <svg v-if="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
      <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
  </div>

  <!-- Mobile drawer overlay -->
  <transition
    enter-active-class="transition ease-out duration-200"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition ease-in duration-150"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div v-if="mobileOpen" class="md:hidden fixed inset-0 z-20 bg-black/40" @click="mobileOpen = false" />
  </transition>

  <transition
    enter-active-class="transition ease-out duration-200"
    enter-from-class="opacity-0 -translate-y-2"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition ease-in duration-150"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 -translate-y-2"
  >
    <div v-if="mobileOpen" class="md:hidden fixed top-[53px] left-0 right-0 z-30 bg-base-100 border-b border-base-200 shadow-lg px-3 pb-4">
      <ul class="menu menu-md gap-0.5 w-full">
        <li class="menu-title px-2 pt-3 pb-0.5">
          <span class="text-xs font-semibold text-base-content/40 uppercase tracking-widest">Quick Actions</span>
        </li>
        <li class="mb-1">
          <button
            class="w-full justify-start gap-3 font-semibold rounded-xl btn bg-gray-200 text-gray-600 border-gray-200 hover:bg-gray-300 hover:border-gray-300"
            @click="$emit('add-transaction'); mobileOpen = false"
          >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 shrink-0">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Add Transaction</span>
          </button>
        </li>
        <li class="menu-title px-2 pt-3 pb-0.5">
          <span class="text-xs font-semibold text-base-content/40 uppercase tracking-widest">Navigation</span>
        </li>
        <li>
          <button
            :class="['w-full justify-start gap-3 font-medium rounded-xl', activePage === 'dashboard' ? 'active' : '']"
            @click="router.get(route('dashboard')); mobileOpen = false"
          >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 shrink-0">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 4.5h7.5v7.5h-7.5zM12.75 4.5h7.5v7.5h-7.5zM12.75 13.5h7.5v7.5h-7.5zM3.75 13.5h7.5v7.5h-7.5z" />
            </svg>
            <span>Dashboard</span>
          </button>
        </li>
        <li>
          <button
            :class="['w-full justify-start gap-3 font-medium rounded-xl', activePage === 'categories' ? 'active' : '']"
            @click="router.get(route('categories.index')); mobileOpen = false"
          >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 shrink-0">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6h.008v.008H6V6z" />
            </svg>
            <span>Categories</span>
          </button>
        </li>
        <li>
          <button
            :class="['w-full justify-start gap-3 font-medium rounded-xl', activePage === 'transactions' ? 'active' : '']"
            @click="router.get(route('transactions.recent')); mobileOpen = false"
          >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 shrink-0">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6h4.5M4.5 12a7.5 7.5 0 1115 0 7.5 7.5 0 01-15 0z" />
            </svg>
            <span>Transactions</span>
          </button>
        </li>
        <li class="menu-title px-2 pt-3 pb-0.5">
          <span class="text-xs font-semibold text-base-content/40 uppercase tracking-widest">Account</span>
        </li>
        <li>
          <button
            class="w-full justify-start gap-3 font-medium rounded-xl"
            @click="router.get(route('profile.edit')); mobileOpen = false"
          >
            <div class="avatar placeholder shrink-0">
              <div class="bg-primary text-primary-content rounded-full w-7">
                <span class="text-xs font-bold">{{ user.name?.charAt(0).toUpperCase() }}</span>
              </div>
            </div>
            <div class="min-w-0">
              <p class="text-sm font-semibold text-base-content truncate leading-tight">{{ user.name }}</p>
              <p class="text-xs text-base-content/50">Profile Settings</p>
            </div>
          </button>
        </li>
      </ul>
    </div>
  </transition>

  <!-- Desktop sidebar (unchanged) -->
  <aside class="hidden md:flex md:flex-col md:w-64 md:min-h-screen bg-base-100 border-r border-base-200 sticky top-0 z-20 shadow-md">
    <!-- Logo -->
    <div class="flex items-center gap-3 px-5 py-4 border-b border-base-200 shrink-0">
      <img src="/public/images/stracker-logo.png" alt="Stracker Logo" class="h-8 w-auto" />
      <span class="font-bold text-base tracking-tight text-base-content">Stracker</span>
    </div>

    <!-- Navigation -->
    <ul class="menu menu-md px-3 py-4 gap-0.5 flex-1 w-full">
      <li class="menu-title px-2 pt-1 pb-0.5">
        <span class="text-xs font-semibold text-base-content/40 uppercase tracking-widest">Quick Actions</span>
      </li>
      <li class="mb-1">
        <button
          class="w-full justify-start gap-3 font-semibold rounded-xl btn bg-gray-200 text-gray-600 border-gray-200 hover:bg-gray-300 hover:border-gray-300"
          @click="$emit('add-transaction')"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.5v15m7.5-7.5h-15" />
          </svg>
          <span>Add Transaction</span>
        </button>
      </li>

      <li class="menu-title px-2 pt-3 pb-0.5">
        <span class="text-xs font-semibold text-base-content/40 uppercase tracking-widest">Navigation</span>
      </li>
      <li>
        <button
          :class="['w-full justify-start gap-3 font-medium rounded-xl', activePage === 'dashboard' ? 'active' : '']"
          @click="router.get(route('dashboard'))"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 4.5h7.5v7.5h-7.5zM12.75 4.5h7.5v7.5h-7.5zM12.75 13.5h7.5v7.5h-7.5zM3.75 13.5h7.5v7.5h-7.5z" />
          </svg>
          <span>Dashboard</span>
        </button>
      </li>
      <li>
        <button
          :class="['w-full justify-start gap-3 font-medium rounded-xl', activePage === 'categories' ? 'active' : '']"
          @click="router.get(route('categories.index'))"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6h.008v.008H6V6z" />
          </svg>
          <span>Categories</span>
        </button>
      </li>
      <li>
        <button
          :class="['w-full justify-start gap-3 font-medium rounded-xl', activePage === 'transactions' ? 'active' : '']"
          @click="router.get(route('transactions.recent'))"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6h4.5M4.5 12a7.5 7.5 0 1115 0 7.5 7.5 0 01-15 0z" />
          </svg>
          <span>Transactions</span>
        </button>
      </li>
    </ul>

    <div class="divider my-0 mx-4 h-px"></div>

    <!-- User profile -->
    <div class="px-3 pb-5 pt-2 shrink-0">
      <button
        class="flex w-full items-center gap-3 rounded-xl p-3 hover:bg-base-200 transition-colors text-left cursor-pointer"
        @click="router.get(route('profile.edit'))"
      >
        <div class="avatar placeholder shrink-0">
          <div class="bg-primary text-primary-content rounded-full w-8">
            <span class="text-xs font-bold">{{ user.name?.charAt(0).toUpperCase() }}</span>
          </div>
        </div>
        <div class="min-w-0">
          <p class="text-sm font-semibold text-base-content truncate leading-tight">{{ user.name }}</p>
          <p class="text-xs text-base-content/50">Profile Settings</p>
        </div>
      </button>
    </div>
  </aside>

  <!-- Mobile spacer to push content below fixed navbar -->
  <div class="md:hidden h-[53px] shrink-0 w-full"></div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import NotificationPopover from '@/Components/NotificationPopover.vue';

defineProps({
  user: { type: Object, required: true },
  activePage: { type: String, default: '' },
});

defineEmits(['add-transaction']);

const mobileOpen = ref(false);
</script>
