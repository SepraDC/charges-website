<template>
  <TransitionRoot appear :show="isOpen" as="template">
    <Dialog as="div" class="relative z-10" @close="$emit('close')">
      <TransitionChild
        as="template"
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black bg-opacity-25" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div
          class="flex min-h-full items-end justify-center text-center md:items-center md:p-4"
        >
          <TransitionChild
            as="template"
            enter="duration-300 transform sm:ease-out"
            enter-from="translate-y-full md:translate-y-0 sm:opacity-0 sm:scale-95"
            enter-to="translate-y-0 sm:opacity-100 sm:scale-100"
            leave="duration-200 ease-in"
            leave-from="translate-y-0 sm:opacity-100 sm:scale-100"
            leave-to="translate-y-full md:translate-y-0 sm:opacity-0 sm:scale-95"
          >
            <DialogPanel
              class="w-full max-w-xl transform overflow-y-scroll rounded-lg bg-white p-6 text-left align-middle shadow-xl transition-all sm:overflow-y-visible md:h-auto"
            >
              <hr
                class="mx-24 mb-4 rounded-full border-t-[3px] border-gray-300 md:hidden"
              >
              <DialogTitle
                as="h3"
                class="text-center text-lg font-medium leading-6 text-orange-300"
              >
                <slot name="title"/>
              </DialogTitle>
              <slot name="body"/>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
<script setup lang="ts">
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle,
} from "@headlessui/vue";

defineProps<{
  isOpen: boolean;
}>();

defineEmits<{
  (e: "close");
}>();
</script>
