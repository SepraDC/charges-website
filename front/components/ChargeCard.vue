<template>
  <div class="flex items-center gap-5 border-b border-gray-300 py-5 last:border-b-0">
    <slot name="img">
      <UButton class="rounded-lg bg-gray-200 p-2 hover:bg-gray-300 focus:outline-gray-300" size="xl"
        @click="$emit('updateState', { id: charge.id, value: !charge.state })">
        <MoneySvg :class="[charge.state ? 'text-green-500' : 'text-red-500']" class="h-12 w-12"></MoneySvg>
      </UButton>
    </slot>
    <div class="flex-1 truncate">
      <p class="mb-2 block font-bold truncate" :title="charge.name">{{ charge.name }}</p>
      <UBadge :label="charge.chargeType?.name" color="primary" variant="subtle" />
    </div>
    <div class="font-extrabold">{{ charge.amount }} €</div>
    <div class="block md:hidden">
      <UDropdownMenu
        :items="[[{ label: 'Modifier', icon: 'i-mdi-edit', onSelect: () => updateChargeOpen = true }, { label: 'Supprimer', icon: 'i-mdi-trash', onSelect: () => confirmPopupOpen = true }]]">
        <UButton icon="i-mdi-menu" variant="ghost" />
      </UDropdownMenu>
    </div>
    <div class="hidden md:flex gap-2">
      <UButton color="error" variant="outline" icon="i-mdi-trash" @click.prevent="confirmPopupOpen = true" />
      <UButton color="secondary" variant="outline" icon="i-mdi-edit" @click.prevent="updateChargeOpen = true" />
    </div>
  </div>
  <teleport to="body">
    <UModal v-model:open="confirmPopupOpen" prevent-close title="Confirmer la suppression ?">
      <template #body>
        <div class="flex flex-col items-center justify-center gap-3 lg:flex-row">
          <UButton size="lg" color="neutral" @click.prevent="closePopup">
            Annuler
          </UButton>
          <UButton size="lg" color="error" @click.prevent="confirmDelete">
            Confirmer
          </UButton>
        </div>
      </template>
    </UModal>
    <UModal v-model:open="updateChargeOpen" prevent-close :title="`Mise à jour: ${charge.name}`">
      <template #body>
        <ChargeForm :initial-value="charge" @on-submit="updateCharge" />
      </template>
    </UModal>
  </teleport>
</template>
<script setup lang="ts">
import type { Charge } from "@type/Charge";
import { ref } from "vue";
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

const updateCharge = (charge: Charge) => {
	updateChargeOpen.value = false;
	emit("updateCharge", { id: props.charge.id, value: charge.value });
};
</script>
