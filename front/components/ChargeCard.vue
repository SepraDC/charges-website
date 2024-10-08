<template>
  <div class="flex items-center gap-5 border-b-2 py-5 last:border-b-0">
    <slot name="img">
      <UButton
        class="rounded-lg bg-gray-200 p-2 hover:bg-gray-300 focus:outline-gray-300"
        size="xl"
        @click="$emit('updateState', { id: charge.id, value: !charge.state })"
      >
        <UIcon name="i-mdi-hand-coin" :class="[charge.state ? 'text-green-500' : 'text-red-500']" class="h-10 w-10" />
      </UButton>
    </slot>
    <div class="flex-1 truncate">
      <p class="mb-2 block font-bold truncate" :title="charge.name">{{ charge.name }}</p>
      <UBadge :label="charge.chargeType?.name" color="orange"/>
    </div>
    <div class="font-extrabold">{{ charge.amount }} €</div>
    <UDropdown v-model="isOpen" class="block md:hidden" :items="[[{label: 'Modifier', icon: 'i-mdi-edit', click: () => updateChargeOpen = true}, {label: 'Supprimer', icon: 'i-mdi-trash', click: () => confirmPopupOpen = true}]]">
        <UButton icon="i-mdi-menu" variant="ghost"/>
      </UDropdown>
    <div class="hidden md:flex gap-2">
      <UButton
        color="red"
        variant="outline"
        icon="i-mdi-trash"
        @click.prevent="confirmPopupOpen = true"
      />
      <UButton
        color="blue"
        variant="outline"
        icon="i-mdi-edit"
        @click.prevent="updateChargeOpen = true"
      />
    </div>
  </div>
  <teleport to="body">
    <ConfirmPopup
      :is-open="confirmPopupOpen"
      @confirm="confirmDelete"
      @cancel="closePopup"
    />
    <UModal v-model="updateChargeOpen">
      <UCard>
        <template #header>
          <h3 class="text-center text-lg font-medium leading-6 text-orange-300">Mise à jour: {{ charge.name }}</h3>
        </template>
        <ChargeForm
          :initial-value="charge"
          @on-submit="updateCharge"
        />
      </UCard>
  </UModal>
  </teleport>
</template>
<script setup lang="ts">
import type { Charge } from "@type/Charge";
import { ref } from "vue";
import ChargeForm from "./ChargeForm.vue";
import ConfirmPopup from "./ConfirmPopup.vue";
const props = defineProps<{
  charge: Charge;
}>();



const confirmPopupOpen = ref<boolean>(false);
const updateChargeOpen = ref<boolean>(false);

const isOpen = ref(false)

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

const updateCharge = (charge: Charge) => {
  updateChargeOpen.value = false;
  emit("updateCharge", { id: props.charge.id, value: charge.value });
};
</script>
