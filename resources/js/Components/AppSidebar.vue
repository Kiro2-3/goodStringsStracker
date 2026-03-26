<template>
  <!-- ===================== MOBILE ===================== -->

  <!-- Mobile top navbar (visible only below md breakpoint) -->
  <div class="md:hidden fixed top-0 left-0 right-0 z-30 bg-base-100 mobile-header-gradient border-b border-base-200 shadow-sm flex items-center justify-between px-4 py-3">
    <div class="flex items-center gap-2">
      <img src="/public/images/str.png" alt="Stracker Logo" class="h-7 w-auto" />
      <span class="font-bold text-base tracking-tight text-base-content">Stracker</span>
    </div>
    <div class="flex items-center gap-2">
      <!-- NotificationPopover removed for mobile view -->
      <!-- Hamburger / close toggle -->
      <button type="button" class="btn btn-ghost btn-sm p-1" @click="mobileOpen = !mobileOpen" aria-label="Toggle menu">
        <svg v-if="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
  </div>

  <!-- Mobile backdrop (dims the page when drawer is open) -->
  <transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div v-if="mobileOpen" class="md:hidden fixed inset-0 z-40 bg-black/50" @click="mobileOpen = false" />
  </transition>

  <!-- Mobile drawer — slides in from the LEFT (Aceternity pattern) -->
  <transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="-translate-x-full opacity-0"
    enter-to-class="translate-x-0 opacity-100"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="translate-x-0 opacity-100"
    leave-to-class="-translate-x-full opacity-0"
  >
    <div
      v-if="mobileOpen"
      class="md:hidden fixed inset-y-0 left-0 z-50 w-72 bg-base-100 shadow-2xl flex flex-col"
    >
      <!-- Drawer header -->
      <div class="flex items-center justify-between px-5 py-4 border-b border-base-200 shrink-0">
        <div class="flex items-center gap-2">
          <img src="/public/images/str.png" alt="Stracker Logo" class="h-7 w-auto" />
          <span class="font-bold text-base tracking-tight text-base-content">Stracker</span>
        </div>
        <button type="button" class="btn btn-ghost btn-sm p-1" @click="mobileOpen = false" aria-label="Close menu">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Drawer nav -->
      <ul class="menu menu-md px-3 py-4 gap-0.5 flex-1 overflow-y-auto w-full">
        <li class="menu-title px-2 pt-1 pb-0.5">
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
      </ul>

      <div class="divider my-0 mx-4 h-px shrink-0"></div>

      <!-- Drawer user profile -->
      <div class="px-3 pb-5 pt-2 shrink-0">
        <button
          class="flex w-full items-center gap-3 rounded-xl p-3 hover:bg-base-200 transition-colors text-left cursor-pointer"
          @click="router.get(route('profile.edit')); mobileOpen = false"
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
    </div>
  </transition>

  <!-- ===================== DESKTOP ===================== -->

  <!--
    Collapsible desktop sidebar (Aceternity pattern):
    - Default: narrow (w-16), shows icons only
    - On hover: expands to w-64, labels fade in
    - Uses CSS transition on width + opacity for smooth animation
  -->
  <aside
    class="hidden md:flex md:flex-col md:min-h-screen bg-base-100 border-r border-base-200 sticky top-0 z-20 shadow-md overflow-hidden transition-all duration-300 ease-in-out h-screen overflow-y-auto sidebar-gradient"
    :style="{ width: desktopOpen ? '16rem' : '4rem' }"
    @mouseenter="desktopOpen = true"
    @mouseleave="desktopOpen = false"
  >
    <!-- Logo area -->
    <div class="flex items-center gap-3 px-4 py-4 border-b border-base-200 shrink-0 min-w-0">
      <img src="/public/images/str.png" alt="Stracker Logo" class="h-8 w-8 shrink-0" />
      <!-- Brand name fades in when expanded -->
      <transition
        enter-active-class="transition-all duration-200 ease-out"
        enter-from-class="opacity-0 -translate-x-2"
        enter-to-class="opacity-100 translate-x-0"
        leave-active-class="transition-all duration-150 ease-in"
        leave-from-class="opacity-100 translate-x-0"
        leave-to-class="opacity-0 -translate-x-2"
      >
        <span v-if="desktopOpen" class="font-bold text-base tracking-tight text-base-content whitespace-nowrap overflow-hidden">Stracker</span>
      </transition>
    </div>

    <!-- Navigation -->
    <ul class="menu menu-md px-2 py-4 gap-0.5 flex-1 w-full overflow-hidden">
      <!-- Section title only shown when expanded -->
      <li v-if="desktopOpen" class="menu-title px-2 pt-1 pb-0.5">
        <span class="text-xs font-semibold text-base-content/40 uppercase tracking-widest whitespace-nowrap">Quick Actions</span>
      </li>
      <li class="mb-1">
        <button
          class="w-full justify-start gap-3 font-semibold rounded-xl btn bg-gray-200 text-gray-600 border-gray-200 hover:bg-gray-300 hover:border-gray-300 !px-2"
          :title="!desktopOpen ? 'Add Transaction' : undefined"
          @click="$emit('add-transaction')"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.5v15m7.5-7.5h-15" />
          </svg>
          <transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-all duration-100 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <span v-if="desktopOpen" class="whitespace-nowrap overflow-hidden">Add Transaction</span>
          </transition>
        </button>
      </li>

      <li v-if="desktopOpen" class="menu-title px-2 pt-3 pb-0.5">
        <span class="text-xs font-semibold text-base-content/40 uppercase tracking-widest whitespace-nowrap">Navigation</span>
      </li>

      <!-- Dashboard -->
      <li>
        <button
          :class="['w-full justify-start gap-3 font-medium rounded-xl !px-2', activePage === 'dashboard' ? 'active' : '']"
          :title="!desktopOpen ? 'Dashboard' : undefined"
          @click="router.get(route('dashboard'))"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 4.5h7.5v7.5h-7.5zM12.75 4.5h7.5v7.5h-7.5zM12.75 13.5h7.5v7.5h-7.5zM3.75 13.5h7.5v7.5h-7.5z" />
          </svg>
          <transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-all duration-100 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <span v-if="desktopOpen" class="whitespace-nowrap overflow-hidden">Dashboard</span>
          </transition>
        </button>
      </li>

      <!-- Categories -->
      <li>
        <button
          :class="['w-full justify-start gap-3 font-medium rounded-xl !px-2', activePage === 'categories' ? 'active' : '']"
          :title="!desktopOpen ? 'Categories' : undefined"
          @click="router.get(route('categories.index'))"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6h.008v.008H6V6z" />
          </svg>
          <transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-all duration-100 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <span v-if="desktopOpen" class="whitespace-nowrap overflow-hidden">Categories</span>
          </transition>
        </button>
      </li>

      <!-- Transactions -->
      <li>
        <button
          :class="['w-full justify-start gap-3 font-medium rounded-xl !px-2', activePage === 'transactions' ? 'active' : '']"
          :title="!desktopOpen ? 'Transactions' : undefined"
          @click="router.get(route('transactions.recent'))"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6h4.5M4.5 12a7.5 7.5 0 1115 0 7.5 7.5 0 01-15 0z" />
          </svg>
          <transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-all duration-100 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <span v-if="desktopOpen" class="whitespace-nowrap overflow-hidden">Transactions</span>
          </transition>
        </button>
      </li>

      <!-- Bank Accounts -->
      <li>
        <button
          :class="['w-full justify-start gap-3 font-medium rounded-xl !px-2', activePage === 'bank-accounts' ? 'active' : '']"
          :title="!desktopOpen ? 'Bank Accounts' : undefined"
          @click="router.get(route('bank-accounts.index'))"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10l9-7 9 7v7a2 2 0 01-2 2H5a2 2 0 01-2-2v-7z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 21V9h6v12" />
          </svg>
          <transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-all duration-100 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <span v-if="desktopOpen" class="whitespace-nowrap overflow-hidden">Bank Accounts</span>
          </transition>
        </button>
      </li>
    </ul>

    <div class="divider my-0 mx-2 h-px shrink-0"></div>

    <!-- User profile -->
    <div class="px-2 pb-5 pt-2 shrink-0">
      <button
        class="flex w-full items-center gap-3 rounded-xl p-2 hover:bg-base-200 transition-colors text-left cursor-pointer overflow-hidden"
        :title="!desktopOpen ? user.name : undefined"
        @click="router.get(route('profile.edit'))"
      >
        <div class="avatar placeholder shrink-0">
          <div class="bg-primary text-primary-content rounded-full w-8">
            <span class="text-xs font-bold">{{ user.name?.charAt(0).toUpperCase() }}</span>
          </div>
        </div>
        <transition
          enter-active-class="transition-all duration-200 ease-out"
          enter-from-class="opacity-0"
          enter-to-class="opacity-100"
          leave-active-class="transition-all duration-100 ease-in"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div v-if="desktopOpen" class="min-w-0">
            <p class="text-sm font-semibold text-base-content truncate leading-tight whitespace-nowrap">{{ user.name }}</p>
            <p class="text-xs text-base-content/50 whitespace-nowrap">Profile Settings</p>
          </div>
        </transition>
      </button>
    </div>
  </aside>

  <!-- Mobile spacer to push page content below the fixed top navbar -->
  <div class="md:hidden h-[53px] shrink-0 w-full"></div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
// import NotificationPopover from '@/Components/NotificationPopover.vue';

defineProps({
  user: { type: Object, required: true },
  // activePage: used to highlight the currently active nav item (e.g. 'dashboard', 'transactions')
  activePage: { type: String, default: '' },
});

// Emits 'add-transaction' so the parent page can open the AddTransaction modal
defineEmits(['add-transaction']);

// Tracks whether the mobile full-screen drawer is open
const mobileOpen = ref(false);

// Desktop: sidebar starts collapsed (icon-only); expands to full width on hover
const desktopOpen = ref(false);
</script>
<style scoped>
.sidebar-gradient {
  background-image: linear-gradient(180deg, rgba(124,58,237,0.03), rgba(245,158,11,0.03));
}

.mobile-header-gradient {
  background-image: linear-gradient(90deg, rgba(124,58,237,0.04), rgba(245,158,11,0.04));
}
</style>
