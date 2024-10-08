<template>
  <section class="container mx-auto grid grid-cols-1 gap-5 p-6 lg:grid-cols-2">
    <div class="col-span-full flex justify-between text-xl font-bold text-gray-800">
      <h1>Bonjour, <br >
        <span class="capitalize text-orange-300">{{
          currentUser?.userIdentifier || "Utilisateur"
        }}</span>
      </h1>
      <div>
        <UButton icon="i-mdi-logout" variant="ghost" label="Deconnexion" @click="signOut"/>
      </div>
    </div>
    <div class="col-span-1 flex flex-col rounded-lg bg-blue-500 p-5 text-white">
      <span>Restant ce mois-ci</span>
      <span class="text-2xl font-extrabold">{{ sumCurrent || 0 }}€</span>
      <span class="self-end">Total</span>
      <span class="self-end text-xl font-extrabold">{{ sum || 0 }}€</span>
    </div>
    <div v-if="charges?.length > 0" class="col-span-1">
        <ChargeCategoryChart :charges="charges" />
    </div>
    <div class="col-span-1">
      <div class="flex justify-between">
        <h1 class="text-xl font-bold text-gray-300">Banques</h1>
      </div>
      <div class="w-full overflow-auto py-5">
        <div class="flex w-fit gap-5">
          <UButton v-for="bank in banks" :key="bank.id" size="xl" color="gray" :class="[selectedBank === bank.id ? 'border-2 border-offset-2 border-orange-500 dark:border-orange-400': '']" :title="bank.name" @click.prevent="updateQuery(bank.id)">
            <img
            class="h-full w-full rounded-lg object-cover"
            :src="bank.image"
            :alt="bank.abbreviation"
            >
          </UButton>
          <UButton icon="i-mdi-view-grid" size="xl" color="gray" :class="[!selectedBank ? 'border-2 border-offset-2 border-orange-500 dark:border-orange-400': '']" title="Tous" @click.prevent="updateQuery()"/>
        </div>
      </div>
    </div>
    <div class="col-span-full h-auto">
      <div class="flex justify-between">
        <h1 class="text-xl font-bold text-gray-300">Prélevements</h1>
        <div class="flex gap-2">
          <UButton
          title="Réinitiliser"
          icon="i-mdi-reload"
          color="orange"
          @click="resetCharges"
          />
          <UButton title="Ajouter" icon="i-mdi-plus" @click="openModal"/>
        </div>
      </div>
      <ChargeCard
      v-for="charge in charges"
      :key="charge['@id']"
      :charge="charge"
      @update-state="updateChargeState"
      @delete-charge="deleteCharge"
      @update-charge="updateCharge"
      />
    </div>
    <teleport to="body">
      <UModal v-model="isOpen">
        <UCard>
          <template #header>
            <h3 class="text-center text-lg font-medium leading-6 text-orange-300">
              Ajouter un prélèvement
            </h3>
          </template>
          <ChargeForm @on-submit="submitCharge"/>
        </UCard>
      </UModal>
    </teleport>
  </section>
</template>
<script lang="ts" setup>
import type { Bank } from "@type/Bank";
import type { Charge } from "@type/Charge";
import { computed, ref } from "vue";
import ChargeCategoryChart from "../components/ChargeCategoryChart.vue";
import ChargeForm from "../components/ChargeForm.vue";
import { getMembers, useApiRoutes } from "../composables/api";

definePageMeta({
  middleware: ["auth"],
});

const currentUser = ref();

const { authUser } = useAuthState();
const { signOut } = useAuth()
const api = useApiRoutes();

currentUser.value = authUser.value;

const sum = computed(() => {
  return charges.value
  ?.reduce((partialSum: number, item: Charge) => partialSum + item.amount, 0)
  .toFixed(2);
});

const sumCurrent = computed(() => {
  return charges.value
  ?.reduce(
  (partialSum: number, item: Charge) =>
  partialSum + (!item.state ? item.amount : 0),
  0
  )
  .toFixed(2);
});

const { data: banks } = useAsyncData<Bank[]>("userBanks", () => {
  return getMembers(api.banks.getUserBanks());
});

const query = ref({ "bank.id": banks.value?.[0]?.id });
const {
  data: charges,
  refresh: refreshCharges
} = useAsyncData<Charge[]>("charges", () => {
  return getMembers(api.charges.getCollection(query.value));
});

const isOpen = ref(false);
const selectedBank = ref<number | null>();

const openModal = () => {
  isOpen.value = true;
};

const closeModal = () => {
  isOpen.value = false;
};

const updateQuery = async (id?: number) => {
  console.log('dedans');
  if (!id) {
    selectedBank.value = null;
    query.value = {};
  } else {
    selectedBank.value = id;
    query.value = { "bank.id": id };
  }
  await refreshCharges();
};

const submitCharge = async (charge: Ref<Charge>) => {
  try {
    await api.charges.create(charge.value);
    closeModal();
  } catch (e) {
    console.error(e);
  }
  await refreshCharges();
};

const updateChargeState = async (charge: { id: string; value: boolean }) => {
  await api.charges.update(charge.id, { state: charge.value });
  await refreshCharges();
};

const updateCharge = async (charge: { id: string; value: Charge }) => {
  try {
    await api.charges.update(charge.id, charge.value);
    await refreshCharges();
  } catch (e) {
    console.error(e);
  }
};

const deleteCharge = async (id: string) => {
  try {
    await api.charges.delete(id);
    await refreshCharges();
  } catch (e) {
    console.error(e);
  }
};

const resetCharges = async () => {
  await api.charges.reset();
  await refreshCharges();
};
</script>
