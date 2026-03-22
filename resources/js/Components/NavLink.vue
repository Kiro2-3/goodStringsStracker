<template>
  <Link
    v-bind="$attrs"
    :class="['btn btn-ghost', active ? 'btn-active' : '', $attrs.class]"
  >
    <slot />
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
// active: adds the btn-active class to visually highlight the current page link
defineProps({
  active: Boolean
});
</script>

<style scoped>
</style>
