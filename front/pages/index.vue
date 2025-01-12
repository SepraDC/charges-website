<template>
  <section class="container mx-auto grid grid-cols-1 gap-5 p-6 lg:grid-cols-2">
    <div class="col-span-full flex justify-between text-xl font-bold text-neutral-500">
      <h1>Bonjour, <br>
        <span class="capitalize text-gold-400">{{
          currentUser?.userIdentifier || "Utilisateur"
        }}</span>
      </h1>
      <div>
        <UButton icon="i-mdi-logout" variant="ghost" color="primary" label="Deconnexion" @click="signOut" />
      </div>
    </div>
    <div class="col-span-1 flex flex-col h-fit rounded-lg bg-blue-500 p-5 text-white">
      <span>Restant ce mois-ci</span>
      <span class="text-2xl font-extrabold">{{ sumCurrent || 0 }}€</span>
      <span class="self-end">Total</span>
      <span class="self-end text-xl font-extrabold">{{ sum || 0 }}€</span>
    </div>
    <div v-if="charges?.length > 0" class="col-span-1 flex gap-5">
      <div class="flex-1">
        <h1 class="text-xl font-bold text-neutral-500">Banques</h1>
        <div class="w-full overflow-auto py-5">
          <div class="flex w-fit gap-5">
            <UButton v-for="bank in banks" :key="bank.id" size="xl" variant="ghost" class="w-15 p-0 rounded-xl"
              :class="[selectedBank === bank.id ? 'border-2 border-offset-2 border-gold-500 dark:border-gold-400' : '']"
              :title="bank.name" @click="updateQuery(bank.id)">
              <img class="h-full w-full rounded-lg object-cover" :src="bank.image" :alt="bank.abbreviation">
            </UButton>
            <UButton icon="i-mdi-view-grid" size="xl" color="gray" class="p-0 rounded-xl w-15" block
              :class="[!selectedBank ? 'border-2 border-offset-2 border-gold-500 dark:border-gold-400' : 'border-2 border-offset-2 border-neutral-500 dark:border-neutral-400']"
              title="Tous" @click="updateQuery()"></UButton>
          </div>
        </div>
      </div>
    </div>
    <div class="col-span-full h-auto">
      <div class="flex justify-between">
        <div class="flex gap-3 justify-center">
          <h1 class=" text-xl font-bold text-neutral-500">Prélevements</h1>
          <UButtonGroup>
            <UButton icon="i-lucide-layout-list" @click="displayList = true" class="lg:rounded-full lg:px-5"
              :color="displayList ? 'primary' : 'neutral'">
            </UButton>
            <UButton icon="i-lucide-chart-pie" @click="displayList = false" class="lg:rounded-full lg:px-5"
              :color="displayList ? 'neutral' : 'primary'">
            </UButton>
          </UButtonGroup>
        </div>
        <div class="flex gap-2">
          <UButton title="Réinitialiser" icon="i-mdi-reload" color="neutral" @click="resetCharges" />
          <LazyUModal v-model:open="createChargeOpen" prevent-close title="Ajouter un prélèvement">
            <UButton title="Ajouter" icon="i-mdi-plus" />
            <template #body>
              <ChargeForm @on-submit="submitCharge" />
            </template>
          </LazyUModal>
        </div>
      </div>
      <template v-if="displayList">
        <LazyChargeCard v-for="charge in charges" :key="charge['@id']" :charge="charge"
          @update-state="updateChargeState" @delete-charge="deleteCharge" @update-charge="updateCharge" />
      </template>
      <template v-else>
        <LazyChargeCategoryChart :charges="charges" />
      </template>
    </div>
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
const { signOut } = useAuth();
const api = useApiRoutes();
const displayList = ref(true);
const createChargeOpen = ref(false);
const toast = useToast();

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
			0,
		)
		.toFixed(2);
});

const { data: banks } = useAsyncData<Bank[]>("userBanks", () => {
	return getMembers(api.banks.getUserBanks());
});

const query = ref({ "bank.id": banks.value?.[0]?.id });
const { data: charges, refresh: refreshCharges } = useAsyncData<Charge[]>(
	"charges",
	() => {
		return getMembers(api.charges.getCollection(query.value));
	},
);
const selectedBank = ref<number | null>();

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

const submitCharge = async (charge: Ref<Charge>) => {
	try {
		await api.charges.create(charge.value);
		await refreshCharges();
		createChargeOpen.value = false;
	} catch (e) {
		console.error(e);
	}
};

const updateChargeState = async (charge: { id: string; value: boolean }) => {
	try {
		await api.charges.update(charge.id, { state: charge.value });
		await refreshCharges();
	} catch (e) {
		toast.add({
			color: "error",
			title: "Une erreur est survenue",
			description: e,
		});
	}
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
