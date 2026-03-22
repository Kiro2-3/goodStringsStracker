<template>
  <TransitionRoot :show="show" as="template">
    <Dialog
      as="div"
      id="modal"
      class="modal modal-open z-50"
      @close="close"
    >
      <TransitionChild
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-base-300/70 backdrop-blur-sm" />
      </TransitionChild>
      <TransitionChild
        enter="ease-out duration-300"
        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        enter-to="opacity-100 translate-y-0 sm:scale-100"
        leave="ease-in duration-200"
        leave-from="opacity-100 translate-y-0 sm:scale-100"
        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
      >
        <DialogPanel :class="['modal-box bg-base-100 text-base-content shadow-xl sm:mx-auto w-full', maxWidthClass]">
          <slot />
        </DialogPanel>
      </TransitionChild>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { Dialog, DialogPanel, TransitionRoot, TransitionChild } from '@headlessui/vue';
import { computed } from 'vue';

const props = defineProps({
  show: Boolean,                          // controls whether the modal is visible
  maxWidth: { type: String, default: '2xl' },  // responsive max-width key (sm|md|lg|xl|2xl)
  closeable: { type: Boolean, default: true }, // when false, clicking outside or pressing Esc does nothing
  onClose: { type: Function, default: () => {} }  // callback invoked when the modal requests to close
});

// Only invoke the close callback when the modal is configured as closeable
const close = () => {
  if (props.closeable) {
    props.onClose();
  }
};

// Map the maxWidth string to the corresponding Tailwind responsive class
const maxWidthClass = computed(() => {
  return {
    sm: 'sm:max-w-sm',
    md: 'sm:max-w-md',
    lg: 'sm:max-w-lg',
    xl: 'sm:max-w-xl',
    '2xl': 'sm:max-w-2xl',
  }[props.maxWidth];
});
</script>

<style scoped>
</style>
