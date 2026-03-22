<template>
  <input
    v-bind="$attrs"
    :type="type"
    :class="['input input-bordered w-full text-black bg-white', $attrs.class]"
    ref="inputRef"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
  />
</template>

<script setup>
import { ref, watch, onMounted, defineProps, defineExpose } from 'vue';

const props = defineProps({
  modelValue: String,          // v-model value bound by the parent
  type: { type: String, default: 'text' },
  isFocused: { type: Boolean, default: false }  // auto-focuses the input on mount when true
});

// Holds a ref to the underlying <input> element so we can call .focus() imperatively
const inputRef = ref(null);

// Exposes focus() to parent components (e.g. for auto-focus after modal opens)
defineExpose({
  focus: () => inputRef.value && inputRef.value.focus()
});

// Reactively focus when isFocused changes after mount
watch(() => props.isFocused, (val) => {
  if (val && inputRef.value) inputRef.value.focus();
});

// Also focus on initial mount if isFocused was already true
onMounted(() => {
  if (props.isFocused && inputRef.value) inputRef.value.focus();
});
</script>

<style scoped>
</style>
