<template>
  <Head title="Register" />
  <div class="login-bg">
    <div class="login-hero-shell">
      <header class="login-hero-nav">
        <div class="login-hero-nav-left">
          <img :src="logoUrl" alt="sTracker preview" class="login-hero-logo shadow-md" />
          <span class="login-hero-brand font-bold text-lg">sTracker</span>
        </div>
        <div class="login-hero-nav-actions">
          <Link :href="route('login')" class="login-hero-link login-hero-link-muted btn btn-ghost btn-sm normal-case">Log in</Link>

        </div>
      </header>

      <main class="login-hero-main">
        <section class="login-hero-grid">
          <div class="login-hero-copy">
            <h1 class="login-hero-title">
              <span class="login-hero-title-highlight" style="background: linear-gradient(90deg, #7c3aed, #FFD700); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Strike your Goals!</span>
            </h1>
            <p class="login-hero-subcopy">
              sTracker turns everyday income and expenses into clear, simple insights so you
              can budget with confidence, avoid surprises, and stay on top of your goals.
            </p>
            <div class="login-hero-cta-row">
              <button
                type="button"
                class="login-hero-primary-cta btn btn-primary btn-lg normal-case"
                @click="showModal = true"
              >Start tracking for free</button>
            </div>
          </div>

          <div class="login-hero-card-wrapper flex flex-col gap-6">
            <div class="login-hero-preview">
              <div class="login-hero-preview-inner">
                <div class="login-inspiration-dashboard-frame">
                  <img :src="previewUrl" alt="sTracker preview" class="login-inspiration-preview-img" />
                </div>
              </div>
            </div>
          </div>
        </section>

        <section class="mt-10 px-3">
          <div class="grid gap-4 md:grid-cols-3">
            <div class="card bg-base-100 shadow-sm border border-base-1600 bg-gray-300">
              <div class="card-body py-4 px-5">
                <h3 class="card-title text-sm font-semibold mb-1 text-base-content">Secure & private</h3>
                <p class="text-xs text-base-content/90">Your data is encrypted in transit and stored safely so only you can see your numbers.</p>
              </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-1600 bg-gray-300">
              <div class="card-body py-4 px-5">
                <h3 class="card-title text-sm font-semibold mb-1 text-base-content">Works across devices</h3>
                <p class="text-xs text-base-content/90">Log in from desktop or mobile and pick up exactly where you left off.</p>
              </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-1600 bg-gray-300">
              <div class="card-body py-4 px-5">
                <h3 class="card-title text-sm font-semibold mb-1 text-base-content">Export when you need</h3>
                <p class="text-xs text-base-content/90">Download your transactions for sharing with your accountant or for backup.</p>
              </div>
            </div>
          </div>
        </section>
      </main>

      <!-- Register Modal -->
      <div
        v-if="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm px-4"
        @click.self="showModal = false"
      >
        <div class="relative w-full max-w-md">
          <div class="bg-white rounded-3xl shadow-2xl px-10 py-10 relative">
            <button
              type="button"
              class="btn btn-circle btn-sm btn-error text-white absolute -right-3 -top-3 shadow-lg"
              @click="showModal = false"
              aria-label="Close"
            >✕</button>

            <!-- Brand -->
            <div class="flex items-center gap-2 mb-8">
              <img :src="logoUrl" alt="sTracker Logo" class="h-6 w-6 rounded-md" />
              <span class="text-sm font-semibold text-gray-700">sTracker</span>
            </div>

            <!-- Heading -->
            <h2 class="text-4xl font-extrabold text-gray-900 leading-tight mb-1">Create<br />your account</h2>
            <p class="text-sm text-gray-400 mb-8">Start tracking your finances for free.</p>

            <form @submit.prevent="submit" class="space-y-4">
              <TextInput
                id="name"
                type="text"
                name="name"
                v-model="form.name"
                autocomplete="name"
                :isFocused="true"
                placeholder="Full name"
                class="input input-bordered w-full bg-gray-50 border-gray-200 text-gray-800 placeholder-gray-400"
                required
              />
              <InputError :message="form.errors.name" class="-mt-2" />

              <TextInput
                id="email"
                type="email"
                name="email"
                v-model="form.email"
                autocomplete="username"
                placeholder="Email address"
                class="input input-bordered w-full bg-gray-50 border-gray-200 text-gray-800 placeholder-gray-400"
                required
              />
              <InputError :message="form.errors.email" class="-mt-2" />

              <TextInput
                id="password"
                type="password"
                name="password"
                v-model="form.password"
                autocomplete="new-password"
                placeholder="Password"
                class="input input-bordered w-full bg-gray-50 border-gray-200 text-gray-800 placeholder-gray-400"
                required
              />
              <InputError :message="form.errors.password" class="-mt-2" />

              <TextInput
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                v-model="form.password_confirmation"
                autocomplete="new-password"
                placeholder="Confirm password"
                class="input input-bordered w-full bg-gray-50 border-gray-200 text-gray-800 placeholder-gray-400"
                required
              />
              <InputError :message="form.errors.password_confirmation" class="-mt-2" />

              <button
                type="submit"
                class="w-full mt-2 py-3 rounded-xl font-bold text-white text-base tracking-wide transition-all"
                style="background: #7c3aed;"
                :disabled="form.processing"
              >
                {{ form.processing ? 'Creating account...' : 'Sign Up' }}
              </button>
            </form>

            <p class="mt-8 text-sm text-gray-400 text-center">
              Already have an account?
              <Link :href="route('login')" class="text-primaryPurple font-semibold hover:underline">Sign In</Link>
            </p>
          </div>
        </div>
      </div>

      <footer class="mt-12 bg-transparent text-black">
        <div class="max-w-6xl mx-auto px-6 py-10 md:py-12">
          <div class="mt-8 pt-4 border-t border-neutral-200 flex flex-col gap-2 text-[11px] text-black/70 md:flex-row md:items-center md:justify-between">
            <span>© 2026 Rocky Penamante. All rights reserved.</span>
          </div>
        </div>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
// Images are imported as URLs so Vite resolves and fingerprints them at build time
import logoUrl from '@/../../public/images/str.png'
import previewUrl from '@/../../public/images/frontview2.png'
import InputError from '@/Components/InputError.vue'
import TextInput from '@/Components/TextInput.vue'

// Modal starts open so the registration form is immediately visible on page load
const showModal = ref(true)

// useForm tracks field values, validation errors, and in-flight state
const form = useForm({
  name:                  '',
  email:                 '',
  password:              '',
  password_confirmation: '',  // must match 'password'; validated server-side
})

// Clears both password fields after every attempt so they never linger in form state
function submit() {
  form.post('/register', {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>

<style scoped>
@import '../../../css/login.css';
</style>
