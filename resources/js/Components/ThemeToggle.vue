<template>
  <div class="flex items-center gap-3">
    <button
      @click="toggle"
      :aria-pressed="isDark.toString()"
      class="btn btn-ghost btn-sm gap-2"
      type="button"
    >
      <svg v-if="!isDark" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.36 6.36l-1.42-1.42M7.05 6.05L5.64 4.64m12.02 0l-1.41 1.41M7.05 17.95l-1.41 1.41M12 7a5 5 0 100 10 5 5 0 000-10z" />
      </svg>
      <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
      </svg>
      <span class="text-sm">{{ isDark ? 'Dark' : 'Light' }}</span>
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const isDark = ref(false)

function applyTheme(value) {
  const root = document.documentElement
  if (value === 'dark') {
    root.classList.add('dark')
    try { root.setAttribute('data-theme', 'dark') } catch (e) {}
    isDark.value = true
  } else {
    root.classList.remove('dark')
    try { root.setAttribute('data-theme', 'purplegold') } catch (e) {}
    isDark.value = false
  }
  // ensure the <body> background utility aligns with theme so gutters match
  try {
    const body = document.body
    body.classList.remove('bg-base-100', 'bg-base-200', 'bg-base-300')
    if (value === 'dark') {
      body.classList.add('bg-base-300')
    } else {
      body.classList.add('bg-base-200')
    }
  } catch (e) {}
}

function toggle() {
  const newTheme = isDark.value ? 'light' : 'dark'
  try { localStorage.setItem('theme', newTheme) } catch (e) {}
  applyTheme(newTheme)
}

onMounted(() => {
  let theme = null
  try { theme = localStorage.getItem('theme') } catch (e) { theme = null }
  if (!theme) {
    // respect system preference
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
    theme = prefersDark ? 'dark' : 'light'
  }
  applyTheme(theme)
})
</script>

<style scoped>
</style>
