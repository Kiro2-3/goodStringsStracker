<template>
  <!-- Only renders when a message is provided; hides itself when there is no error -->
  <div v-if="message" :class="['alert alert-error mt-2 py-2', $attrs.class]" v-bind="$attrs">
    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2m8-8l2 2m0 0l2 2m-2-2l-2 2m2-2l2-2" />
    </svg>
    <span class="text-sm">{{ message }}</span>
  </div>
</template>

<script setup>
// message: the validation error string to display (passed from parent form fields)
defineProps({
  message: String
});
</script>

<style scoped>
</style>
