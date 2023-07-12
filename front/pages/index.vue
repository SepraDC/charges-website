<template>
  <section class="container mx-auto grid grid-cols-1 gap-5 p-6 lg:grid-cols-2">
    <h1 class="col-span-full text-xl font-bold text-gray-800">
      Bonjour, <br />
      <span class="capitalize text-orange-300">{{
        currentUser?.userIdentifier || "Utilisateur"
      }}</span>
    </h1>
    <div class="col-span-1 flex flex-col rounded-lg bg-blue-500 p-5 text-white">
      <span>Restant ce mois-ci</span>
      <span class="text-2xl font-extrabold">{{ sumCurrent || 0 }}€</span>
      <span class="self-end">Total</span>
      <span class="self-end text-xl font-extrabold">{{ sum || 0 }}€</span>
    </div>
    <div class="col-span-1">
      <div class="flex justify-between">
        <h1 class="text-xl font-bold text-gray-300">Banques</h1>
      </div>
      <div class="w-full overflow-auto py-5">
        <div class="flex w-fit gap-5">
          <button
            v-for="bank in banks"
            :key="bank.id"
            :title="bank.name"
            class="aspect-square w-14 rounded-lg bg-gray-200 p-3 hover:bg-gray-300 focus:outline-orange-300"
            :class="[
              selectedBank === bank.id ? 'border-2 border-orange-300' : '',
            ]"
            @click.prevent="updateQuery(bank.id)"
          >
            <img
              class="h-full w-full rounded-lg object-cover"
              :src="bank.image"
              :alt="bank.abbreviation"
            />
          </button>
          <button
            title="Tous"
            class="aspect-square w-14 rounded-lg bg-gray-200 p-3 hover:bg-gray-300 focus:outline-orange-300"
            :class="[!selectedBank ? 'border-2 border-orange-300' : '']"
            @click.prevent="updateQuery()"
          >
            <InlineSvg
              class="h-full w-full text-gray-800"
              src="/svg/all.svg"
            ></InlineSvg>
          </button>
        </div>
      </div>
    </div>
    <div class="col-span-full h-auto">
      <div class="flex justify-between">
        <h1 class="text-xl font-bold text-gray-300">Prélevements</h1>
        <div class="flex gap-2">
          <button
            title="Réinitiliser"
            class="flex h-7 w-7 items-center justify-center rounded bg-orange-300 p-0 text-3xl font-extrabold text-white hover:bg-orange-400 focus:outline-orange-400"
            @click.prevent="resetCharges"
          >
            <InlineSvg src="/svg/arrow.svg" class="w-ful h-full"></InlineSvg>
          </button>
          <button
            title="Ajouter"
            class="flex h-7 w-7 items-center justify-center rounded bg-green-300 p-0 text-3xl font-extrabold text-white hover:bg-green-400 focus:outline-green-400"
            @click.prevent="openModal"
          >
            <InlineSvg
              src="/svg/plus.svg"
              class="h-full w-full text-white"
            ></InlineSvg>
          </button>
        </div>
      </div>
      <ChargeCard
        v-for="charge in charges"
        :key="charge['@id']"
        :charge="charge"
        @updateState="updateChargeState"
        @deleteCharge="deleteCharge"
        @updateCharge="updateCharge"
      />
    </div>
    <ModalBottomSheet :is-open="isOpen" @close="closeModal">
      <template #title>Ajouter un prélèvement</template>
      <template #body>
        <ChargeForm @onSubmit="submitCharge"></ChargeForm>
      </template>
    </ModalBottomSheet>
  </section>
</template>
<script lang="ts" setup>
import { ref } from "vue";
import InlineSvg from "vue-inline-svg";
import { useApiRoutes, getMembers } from "../composables/api";
import { ChargeType } from "../@type/ChargeType";
import { Bank } from "../@type/Bank";
import { Charge } from "../@type/Charge";
import ChargeForm from "../components/ChargeForm.vue";
import ModalBottomSheet from "../components/ModalBottomSheet.vue";

definePageMeta({
  middleware: ["auth"],
});

const currentUser = ref();

const { authUser } = useAuthState();
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

const { data: banks } = useAsyncData<never, never, Bank[]>("userBanks", () => {
  return getMembers(api.banks.getUserBanks());
});

const { data: chargeTypes } = useAsyncData<never, never, ChargeType[]>(
  "chargeTypes",
  () => {
    return getMembers(api.chargeTypes.getCollection());
  }
);

const query = ref({ "bank.id": banks.value?.[0]?.id });
const {
  data: charges,
  error,
  refresh: refreshCharges,
} = useAsyncData<never, never, Charge[]>("charges", () => {
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
  if (!id) {
    selectedBank.value = null;
    query.value = {};
  } else {
    selectedBank.value = id;
    query.value = { "bank.id": id };
  }
  await refreshCharges();
};

const submitCharge = async (charge: any) => {
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
