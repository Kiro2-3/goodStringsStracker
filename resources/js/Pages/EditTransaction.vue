<template>
  <Modal :show="true" maxWidth="md" :onClose="closeModal">
    <!-- Header -->
    <div class="flex items-start justify-between gap-3">
      <div class="flex items-center gap-3">
        <div class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.862 4.487l1.687-1.687a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.5 7.125L16.875 4.5" />
          </svg>
        </div>
        <div>
          <h2 class="text-xl font-bold text-base-content">Edit Transaction</h2>
          <p class="text-sm text-base-content/55">Update the details of this record.</p>
        </div>
      </div>
      <button
        type="button"
        class="btn btn-ghost btn-sm btn-circle shrink-0 text-base-content/50 hover:text-base-content"
        @click="closeModal"
        aria-label="Close"
      >✕</button>
    </div>

    <div class="divider my-4"></div>

    <form @submit.prevent="submit" class="space-y-4">
      <!-- Description + Amount -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="form-control gap-1">
          <label class="label py-0" for="description">
            <span class="label-text font-semibold">Description</span>
          </label>
          <input
            id="description"
            type="text"
            class="input input-bordered w-full"
            placeholder="e.g. Grocery run"
            v-model="form.description"
          />
          <InputError v-if="errors.description" :message="errors.description" />
        </div>
        <div class="form-control gap-1">
          <label class="label py-0" for="amount">
            <span class="label-text font-semibold">Amount</span>
          </label>
          <input
            id="amount"
            type="number"
            step="0.01"
            min="0"
            class="input input-bordered w-full"
            placeholder="0.00"
            v-model="form.amount"
          />
          <InputError v-if="errors.amount" :message="errors.amount" />
        </div>
      </div>

      <!-- Type + Category -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="form-control gap-1">
          <label class="label py-0" for="type">
            <span class="label-text font-semibold">Type</span>
          </label>
          <select
            id="type"
            class="select select-bordered w-full opacity-60"
            v-model="form.type"
            disabled
          >
            <option value="income">Income</option>
            <option value="expense">Expense</option>
          </select>
          <InputError v-if="errors.type" :message="errors.type" />
        </div>
        <div class="form-control gap-1">
          <label class="label py-0" for="category">
            <span class="label-text font-semibold">Category</span>
          </label>
          <select
            id="category"
            class="select select-bordered w-full"
            :class="form.type === 'income' ? 'opacity-60' : ''"
            v-model="form.category"
            :disabled="form.type === 'income'"
          >
            <option v-if="form.type === 'income'" value="Salary">Salary</option>
            <template v-else>
              <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </template>
          </select>
          <InputError v-if="errors.category" :message="errors.category" />
        </div>
      </div>

      <!-- Date -->
      <div class="form-control gap-1">
        <label class="label py-0" for="entry_date">
          <span class="label-text font-semibold">Date</span>
        </label>
        <input
          id="entry_date"
          type="date"
          class="input input-bordered w-full"
          v-model="form.entry_date"
        />
        <InputError v-if="errors.entry_date" :message="errors.entry_date" />
      </div>

      <!-- Actions -->
      <div class="modal-action mt-2">
        <button type="button" class="btn btn-ghost" @click="closeModal">Cancel</button>
        <button type="submit" class="btn btn-primary" :disabled="processing">
          <span v-if="processing" class="loading loading-spinner loading-sm"></span>
          Update Transaction
        </button>
      </div>
    </form>
  </Modal>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import InputError from '@/Components/InputError.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  transaction: Object,
  categories: {
    type:    Array,
    default: () => ['Food', 'Rent', 'Leisure', 'Bills'],
  },
})

const emit = defineEmits(['close'])

const form = ref({
  description: props.transaction.description,
  amount:      props.transaction.amount,
  type:        props.transaction.type,
  category:    props.transaction.category,
  entry_date:  props.transaction.entry_date,
})

const errors     = ref({})
const processing = ref(false)

function submit() {
  processing.value = true

  router.put(route('transactions.update', props.transaction.id), form.value, {
    preserveScroll: true,
    onSuccess: () => {
      errors.value     = {}
      processing.value = false
      emit('close')
    },
    onError: (e) => {
      errors.value     = e
      processing.value = false
    },
  })
}

function closeModal() {
  emit('close')
}
</script>

<style scoped>

</style>
