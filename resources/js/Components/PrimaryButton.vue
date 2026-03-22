<template>
  <button
    v-bind="$attrs"
    :class="['btn btn-primary', disabled ? 'opacity-50 cursor-not-allowed' : '', $attrs.class]"
    :disabled="disabled"
  >
    <slot />
  </button>
</template>

<script setup>
// disabled: greys out the button and prevents clicks (used during form submissions)
defineProps({
  disabled: Boolean
});
</script>

<style scoped>
</style>
