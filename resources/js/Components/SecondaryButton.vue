<template>
  <button
    v-bind="$attrs"
    :type="type"
    :class="['btn', disabled ? 'opacity-50 cursor-not-allowed' : '', $attrs.class]"
    :disabled="disabled"
  >
    <slot />
  </button>
</template>

<script setup>
// type: defaults to 'button' so it won't accidentally submit enclosing forms
// disabled: greys out the button and prevents clicks
defineProps({
  type: { type: String, default: 'button' },
  disabled: Boolean
});
</script>

<style scoped>
</style>
