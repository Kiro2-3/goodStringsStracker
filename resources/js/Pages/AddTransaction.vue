<template>
  <Modal :show="true" maxWidth="md" :onClose="closeModal">
    <div class="w-full max-w-lg p-8 bg-[#f7f6f3] dark:bg-gray-900 rounded-2xl shadow-lg transition-colors border border-[#e3e1db]">
      <div class="mb-8 flex items-center justify-between">
        <h2 class="font-semibold text-2xl text-[#222222] dark:text-gray-100 tracking-tight">Add Transaction</h2>
        <button type="button" class="rounded-full p-2 hover:bg-[#eceae4] dark:hover:bg-gray-800 transition" @click="closeModal" aria-label="Close">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
      </div>
      <form @submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="form-control">
            <InputLabel value="Description" htmlFor="description" />
            <TextInput
              id="description"
              type="text"
              placeholder="Description"
              v-model="form.description"
            />
            <InputError v-if="errors.description" :message="errors.description" />
          </div>
          <div class="form-control">
            <InputLabel value="Amount" htmlFor="amount" />
            <TextInput
              id="amount"
              type="number"
              step="0.01"
              min="0"
              placeholder="0.00"
              v-model="form.amount"
            />
            <InputError v-if="errors.amount" :message="errors.amount" />
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="form-control">
            <InputLabel value="Type" htmlFor="type" />
            <select
              id="type"
              class="select select-bordered w-full text-gray-800 bg-gray-50 border border-gray-300 focus:border-blue-400 focus:ring-blue-400 dark:bg-gray-800 dark:text-white dark:border-gray-700"
              v-model="form.type"
            >
              <option value="income">Income</option>
              <option value="expense">Expense</option>
            </select>
            <InputError v-if="errors.type" :message="errors.type" />
          </div>
          <div class="form-control">
            <InputLabel value="Category" htmlFor="category" />
            <select
              id="category"
              class="select select-bordered w-full text-gray-800 bg-gray-50 border border-gray-300 focus:border-blue-400 focus:ring-blue-400 dark:bg-gray-800 dark:text-white dark:border-gray-700"
              :class="form.type === 'income' ? 'opacity-50 cursor-not-allowed' : ''"
              v-model="form.category"
              :disabled="form.type === 'income'"
            >
              <option v-if="form.type === 'income'" value="Salary">Salary</option>
              <template v-else>
                <option v-for="cat in expenseCategories" :key="cat" :value="cat">{{ cat }}</option>
              </template>
            </select>
            <InputError v-if="errors.category" :message="errors.category" />
          </div>
        </div>
        <div class="form-control">
          <InputLabel value="Date" htmlFor="entry_date" />
          <input
            id="entry_date"
            type="date"
            class="input input-bordered w-full text-gray-800 bg-gray-50 border border-gray-300 focus:border-blue-400 focus:ring-blue-400 dark:bg-gray-800 dark:text-white dark:border-gray-700"
            v-model="form.entry_date"
          />
          <InputError v-if="errors.entry_date" :message="errors.entry_date" />
        </div>
        <div class="flex justify-end mt-8">
          <PrimaryButton
            type="submit"
            class="w-full md:w-1/3 text-base bg-[#ff5a5f] hover:bg-[#e04850] text-white font-semibold rounded-lg shadow-sm border-none transition dark:bg-blue-500 dark:hover:bg-blue-600"
            :disabled="processing"
          >
            Save Transaction
          </PrimaryButton>
        </div>
      </form>
    </div>
  </Modal>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  categories: {
    type: Array,
    default: () => ["Food", "Rent", "Leisure", "Bills"]
  }
});

const emit = defineEmits(['close']);

const form = ref({
  description: '',
  amount: '',
  type: 'expense',
  category: 'Food',
  entry_date: new Date().toISOString().split('T')[0],
});
const errors = ref({});
const processing = ref(false);

// Categories to use when type is expense (exclude 'Salary' if present)
const expenseCategories = computed(() => props.categories.filter(cat => cat !== 'Salary'));

watch(() => form.value.type, (newType) => {
  if (newType === 'income') {
    form.value.category = 'Salary';
  } else if (newType === 'expense' && form.value.category === 'Salary') {
    form.value.category = 'Food';
  }
});

function submit() {
  processing.value = true;
  router.post(route('transactions.store'), form.value, {
    onSuccess: () => {
      form.value = {
        description: '',
        amount: '',
        type: 'expense',
        category: 'Food',
        entry_date: new Date().toISOString().split('T')[0],
      };
      errors.value = {};
      processing.value = false;
      emit('close');
    },
    onError: (e) => {
      errors.value = e;
      processing.value = false;
    }
  });
}

function closeModal() {
  emit('close');
}
</script>

<style scoped>

</style>
