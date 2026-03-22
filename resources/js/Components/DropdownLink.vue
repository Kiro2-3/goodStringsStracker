
<template>
  <Link
    v-bind="{...$attrs, ...$props}"
    :href="href"
    :method="method"
    :as="as"
    class="dropdown-link"
  >
    <slot />
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  href: String,
  // method: HTTP verb used by Inertia's Link (default 'get'; use 'post'/'delete' for actions like logout)
  method: {
    type: String,
    default: 'get',
  },
  // as: renders the link as the given HTML tag (default 'a'; use 'button' for non-GET methods)
  as: {
    type: String,
    default: 'a',
  },
});

if (import.meta.env.DEV) {
  // Debug log only in development; helps verify the correct href is passed at runtime
  console.log('DropdownLink href:', props.href);
}
</script>

<style scoped>
.dropdown-link {
  display: block;
  width: 100%;
  padding: 0.5rem 1rem;
  text-align: left;
  background: none;
  border: none;
  cursor: pointer;
}
</style>
