<template>
  <button
    v-bind="$attrs"
    :class="['btn btn-error', disabled ? 'opacity-50 cursor-not-allowed' : '', $attrs.class]"
    :disabled="disabled"
  >
    <slot />
  </button>
</template>

<script setup>
// Accepts a disabled prop to visually grey out the button and block clicks
defineProps({
  disabled: Boolean
});
</script>

<style scoped>
</style>
