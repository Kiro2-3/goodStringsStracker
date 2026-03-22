<template>
  <AuthenticatedLayout :user="auth.user">
    <Head title="Profile Settings" />

    <div class="min-h-screen w-full flex flex-col md:flex-row bg-base-200 text-base-content">
      <!-- Sidebar -->
      <AppSidebar
        :user="auth.user"
        active-page=""
        @add-transaction="showAddTransaction = true"
      />

      <!-- Main content -->
      <main class="flex-1 min-w-0 px-4 md:px-12 py-8 space-y-6">
        <!-- Flash success -->
        <div v-if="status === 'profile-updated'" class="alert alert-success shadow">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Profile updated successfully.</span>
        </div>

        <!-- Profile Info Card -->
        <div class="card bg-base-100 border border-base-200 shadow-xl">
          <div class="card-body gap-5">
            <div>
              <h2 class="text-lg font-bold text-base-content">Profile Information</h2>
              <p class="text-sm text-base-content/60 mt-0.5">Update your name and email address.</p>
            </div>

            <form @submit.prevent="submitProfile" class="space-y-4">
              <label class="form-control w-full gap-1">
                <span class="label-text font-semibold">Name</span>
                <input
                  v-model="profileForm.name"
                  type="text"
                  class="input input-bordered w-full bg-base-100"
                  required
                />
                <span v-if="profileForm.errors.name" class="label-text-alt text-error">{{ profileForm.errors.name }}</span>
              </label>

              <label class="form-control w-full gap-1">
                <span class="label-text font-semibold">Email</span>
                <input
                  v-model="profileForm.email"
                  type="email"
                  class="input input-bordered w-full bg-base-100"
                  required
                />
                <span v-if="profileForm.errors.email" class="label-text-alt text-error">{{ profileForm.errors.email }}</span>
              </label>

              <div class="flex justify-end">
                <button type="submit" class="btn btn-primary" :disabled="profileForm.processing">
                  Save Changes
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Change Password Card -->
        <div class="card bg-base-100 border border-base-200 shadow-xl">
          <div class="card-body gap-5">
            <div>
              <h2 class="text-lg font-bold text-base-content">Change Password</h2>
              <p class="text-sm text-base-content/60 mt-0.5">Ensure your account uses a strong password.</p>
            </div>

            <div v-if="status === 'password-updated'" class="alert alert-success shadow">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>Password updated successfully.</span>
            </div>

            <form @submit.prevent="submitPassword" class="space-y-4">
              <label class="form-control w-full gap-1">
                <span class="label-text font-semibold">Current Password</span>
                <input
                  v-model="passwordForm.current_password"
                  type="password"
                  class="input input-bordered w-full bg-base-100"
                  autocomplete="current-password"
                  required
                />
                <span v-if="passwordForm.errors.current_password" class="label-text-alt text-error">{{ passwordForm.errors.current_password }}</span>
              </label>

              <label class="form-control w-full gap-1">
                <span class="label-text font-semibold">New Password</span>
                <input
                  v-model="passwordForm.password"
                  type="password"
                  class="input input-bordered w-full bg-base-100"
                  autocomplete="new-password"
                  required
                />
                <span v-if="passwordForm.errors.password" class="label-text-alt text-error">{{ passwordForm.errors.password }}</span>
              </label>

              <label class="form-control w-full gap-1">
                <span class="label-text font-semibold">Confirm New Password</span>
                <input
                  v-model="passwordForm.password_confirmation"
                  type="password"
                  class="input input-bordered w-full bg-base-100"
                  autocomplete="new-password"
                  required
                />
                <span v-if="passwordForm.errors.password_confirmation" class="label-text-alt text-error">{{ passwordForm.errors.password_confirmation }}</span>
              </label>

              <div class="flex justify-end">
                <button type="submit" class="btn btn-primary" :disabled="passwordForm.processing">
                  Update Password
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Danger Zone Card -->
        <div class="card bg-base-100 border border-base-200 shadow-xl">
          <div class="card-body gap-5">
            <div>
              <h2 class="text-lg font-bold text-base-content">Account</h2>
              <p class="text-sm text-base-content/60 mt-0.5">Manage your session and account.</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
              <button
                type="button"
                @click="logout"
                class="btn btn-outline btn-error gap-2"
              >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 6.75L21 12m0 0-5.25 5.25M21 12H9m3.75 7.5H6.75A2.25 2.25 0 014.5 17.25v-10.5A2.25 2.25 0 016.75 4.5h6" />
                </svg>
                Logout
              </button>

              <button
                type="button"
                @click="showDeleteModal = true"
                class="btn btn-outline btn-error gap-2 opacity-70"
              >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete Account
              </button>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Add Transaction Modal -->
    <AddTransaction
      v-if="showAddTransaction"
      :categories="[]"
      @close="showAddTransaction = false"
    />

    <!-- Delete Account Modal -->
    <div v-if="showDeleteModal" class="modal modal-open">
      <div class="modal-box text-base-content">
        <h3 class="font-bold text-lg text-error">Delete Account</h3>
        <p class="py-3 text-sm text-base-content/70">
          This action is permanent and cannot be undone. Please enter your password to confirm.
        </p>
        <form @submit.prevent="submitDelete" class="space-y-4">
          <label class="form-control w-full gap-1">
            <span class="label-text font-semibold">Password</span>
            <input
              v-model="deleteForm.password"
              type="password"
              class="input input-bordered w-full"
              placeholder="Enter your password"
              required
            />
            <span v-if="deleteForm.errors.password" class="label-text-alt text-error">{{ deleteForm.errors.password }}</span>
          </label>
          <div class="modal-action">
            <button type="button" @click="showDeleteModal = false" class="btn btn-ghost">Cancel</button>
            <button type="submit" class="btn btn-error" :disabled="deleteForm.processing">Delete Account</button>
          </div>
        </form>
      </div>
      <div class="modal-backdrop" @click="showDeleteModal = false"></div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue';
import AppSidebar from '@/Components/AppSidebar.vue';
import AddTransaction from '@/Pages/AddTransaction.vue';

const props = defineProps({
  auth: Object,
  mustVerifyEmail: Boolean,  // when true, shows an email-verification notice
  status: String,            // server feedback key: 'profile-updated' | 'password-updated'
});

// useForm provides .processing, .errors, .patch/.put/.delete helpers with Inertia integration
const profileForm = useForm({
  name: props.auth.user.name,
  email: props.auth.user.email,
});

// Separate form for account deletion (requires password for safety)
const deleteForm = useForm({
  password: '',
});

const showDeleteModal   = ref(false)
const showAddTransaction = ref(false)

// Separate form keeps password change errors isolated from profile errors
const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

// Resets password fields after a successful update so they don't linger in the form
function submitPassword() {
  passwordForm.put(route('password.update'), {
    onSuccess: () => passwordForm.reset(),
  });
}

// PATCH is used because only name/email change; not a full resource replacement
function submitProfile() {
  profileForm.patch(route('profile.update'));
}

function logout() {
  router.post(route('logout'), {}, {
    onSuccess: () => {
      router.visit(route('login'));
    },
  });
}

// Requires password confirmation before permanently deleting the account
function submitDelete() {
  deleteForm.delete(route('profile.destroy'), {
    onSuccess: () => {
      router.visit(route('login'));
    },
  });
}
</script>
