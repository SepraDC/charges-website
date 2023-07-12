<script setup lang="ts">
import ModalBottomSheet from "./ModalBottomSheet.vue";

defineProps<{
  isOpen: boolean;
}>();

defineEmits<{
  confirm;
  cancel;
}>();
</script>

<template>
  <ModalBottomSheet :is-open="isOpen" @close="$emit('cancel')">
    <template #title> Confirmer la suppression ? </template>
    <template #body>
      <div
        class="mt-5 flex flex-col items-center justify-center gap-5 lg:flex-row"
      >
        <button
          class="rounded-md bg-gray-400 px-5 py-2 text-white hover:bg-gray-500 focus:outline-gray-600"
          @click.prevent="$emit('cancel')"
        >
          Annuler
        </button>
        <button
          class="rounded-md bg-red-400 px-5 py-2 text-white hover:bg-red-500 focus:outline-red-600"
          @click.prevent="$emit('confirm')"
        >
          Confirmer
        </button>
      </div>
    </template>
  </ModalBottomSheet>
</template>

<style scoped></style>
