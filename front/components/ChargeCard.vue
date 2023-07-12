<template>
  <div class="flex items-center gap-5 border-b-2 py-5 last:border-b-0">
    <slot name="img">
      <button
        class="rounded-lg bg-gray-200 p-1 hover:bg-gray-300 focus:outline-gray-300"
        @click="$emit('updateState', { id: charge.id, value: !charge.state })"
      >
        <InlineSvg
          src="/svg/money-3.svg"
          class="h-10 w-10 lg:h-16 lg:w-16"
          :class="[charge.state ? 'text-green-500' : 'text-red-500']"
        ></InlineSvg>
      </button>
    </slot>
    <div class="flex-1">
      <span class="mb-2 block font-bold">{{ charge.name }}</span>
      <span
        class="rounded-md bg-orange-300 px-4 py-1 text-sm font-bold text-white"
      >
        {{ charge.chargeType?.name }}
      </span>
    </div>
    <div class="font-extrabold">{{ charge.amount }} €</div>
    <div class="flex gap-2">
      <button
        class="h-7 w-7 focus:outline-red-600"
        @click.prevent="confirmPopupOpen = true"
      >
        <InlineSvg
          class="h-full w-full text-red-500 hover:text-red-600"
          src="/svg/delete.svg"
        ></InlineSvg>
      </button>
      <button
        class="h-7 w-7 focus:outline-blue-600"
        @click.prevent="updateChargeOpen = true"
      >
        <InlineSvg
          class="h-full w-full text-blue-500 hover:text-blue-600"
          src="/svg/edit.svg"
        ></InlineSvg>
      </button>
    </div>
  </div>
  <teleport to="body">
    <ConfirmPopup
      :is-open="confirmPopupOpen"
      @confirm="confirmDelete"
      @cancel="closePopup"
    ></ConfirmPopup>
    <ModalBottomSheet
      :is-open="updateChargeOpen"
      @close="updateChargeOpen = false"
    >
      <template #title>Mise à jour : {{ charge.name }}</template>
      <template #body>
        <ChargeForm
          :initial-value="charge"
          @onSubmit="updateCharge"
        ></ChargeForm>
      </template>
    </ModalBottomSheet>
  </teleport>
</template>
<script setup lang="ts">
import InlineSvg from "vue-inline-svg";
import { ref } from "vue";
import { Charge } from "../@type/Charge";
import ConfirmPopup from "./ConfirmPopup.vue";
import ModalBottomSheet from "./ModalBottomSheet.vue";
import ChargeForm from "./ChargeForm.vue";

const props = defineProps<{
  charge: Charge;
}>();

const confirmPopupOpen = ref<boolean>(false);
const updateChargeOpen = ref<boolean>(false);

const emit = defineEmits<{
  updateState: [id: string, value: boolean];
  updateCharge: [id: string, value: Charge];
  deleteCharge: [id: number];
}>();

const closePopup = () => {
  confirmPopupOpen.value = false;
};

const confirmDelete = () => {
  confirmPopupOpen.value = false;
  emit("deleteCharge", props.charge.id);
};

const updateCharge = (charge: any) => {
  updateChargeOpen.value = false;
  emit("updateCharge", { id: props.charge.id, value: charge.value });
};
</script>
