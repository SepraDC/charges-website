<template>
    <section
        class="container mx-auto grid grid-cols-1 gap-5 p-6 lg:grid-cols-2"
    >
        <div
            class="col-span-full flex justify-between text-xl font-bold text-neutral-500"
        >
            <h1>
                Bonjour, <br />
                <span class="capitalize text-gold-400">{{
                    authUser &&
                    authUser?.userIdentifier || "Utilisateur"
                }}</span>
            </h1>
            <div>
                <UButton
                    icon="i-mdi-logout"
                    variant="ghost"
                    color="primary"
                    label="Deconnexion"
                    @click="signOut"
                />
            </div>
        </div>
        <div
            class="col-span-1 flex flex-col h-fit rounded-lg bg-blue-500 p-5 text-white"
        >
            <span>Restant ce mois-ci</span>
            <span class="text-2xl font-extrabold">{{ sumCurrent || 0 }}€</span>
            <span class="self-end">Total</span>
            <span class="self-end text-xl font-extrabold">{{ sum || 0 }}€</span>
        </div>
        <div v-if="data?.userBanks && data.userBanks.length > 0" class="col-span-1 flex gap-5">
            <div class="flex-1">
                <h1 class="text-xl font-bold text-neutral-500">Banques</h1>
                <div class="w-full overflow-auto py-5">
                    <div class="flex w-fit gap-5">
                        <UButton
                            v-for="bank in data?.userBanks"
                            :key="bank.id"
                            size="xl"
                            variant="ghost"
                            class="w-15 p-0 rounded-xl"
                            :class="[
                                selectedBank === bank.id
                                    ? 'border-2 border-offset-2 border-gold-500 dark:border-gold-400'
                                    : '',
                            ]"
                            :title="bank.name"
                            @click="updateQuery(bank.id)"
                        >
                            <img
                                class="h-full w-full rounded-lg object-cover"
                                :src="bank.image"
                                :alt="bank.abbreviation"
                            />
                        </UButton>
                        <UButton
                            icon="i-mdi-view-grid"
                            size="xl"
                            color="neutral"
                            class="p-0 rounded-xl w-15"
                            block
                            :class="[
                                !selectedBank
                                    ? 'border-2 border-offset-2 border-gold-500 dark:border-gold-400'
                                    : 'border-2 border-offset-2 border-neutral-500 dark:border-neutral-400',
                            ]"
                            title="Tous"
                            @click="updateQuery()"
                        ></UButton>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-full h-auto flex flex-col gap-5">
            <div class="flex justify-between">
                <div class="flex gap-3 justify-center">
                    <h1 class="text-xl font-bold text-neutral-500">
                        Prélevements
                    </h1>
                    <UFieldGroup>
                        <UButton
                            icon="i-lucide-layout-list"
                            @click="displayList = true"
                            class="lg:rounded-full lg:px-5"
                            :color="displayList ? 'primary' : 'neutral'"
                        >
                        </UButton>
                        <UButton
                            icon="i-lucide-chart-pie"
                            @click="displayList = false"
                            class="lg:rounded-full lg:px-5"
                            :color="displayList ? 'neutral' : 'primary'"
                        >
                        </UButton>
                    </UFieldGroup>
                </div>
                <div class="flex gap-2">
                    <UButton
                        title="Réinitialiser"
                        icon="i-mdi-reload"
                        color="neutral"
                        @click="resetCharges"
                    />
                    <LazyUModal
                        v-model:open="createChargeOpen"
                        prevent-close
                        title="Ajouter un prélèvement"
                    >
                        <UButton title="Ajouter" icon="i-mdi-plus" />
                        <template #body>
                            <ChargeForm @on-submit="submitCharge" />
                        </template>
                    </LazyUModal>
                </div>
            </div>
            <template v-if="displayList">
                <template v-for="group in groupedCharges" :key="group.dayOfWithdrawal">
                    <USeparator>
                       <UBadge variant="soft" :label="formatDayLabel(group.dayOfWithdrawal)" />
                    </USeparator>
                    <LazyChargeCard
                        v-for="charge in group.charges"
                        :key="charge['@id']"
                        :charge="charge"
                        @update-state="(charge) => updateChargeState({ id: charge.id, value: charge.value })"
                        @delete-charge="(id: number) => deleteCharge(String(id))"
                        @update-charge="({id, value}) => updateCharge(id, value)"
                    />
                </template>
            </template>
            <template v-else>
                <LazyChargeCategoryChart :charges="data?.charges || []" />
            </template>
        </div>
    </section>
</template>
<script lang="ts" setup>
import { computed, ref } from "vue";
import type { Bank } from "../@type/Bank";
import type { Charge } from "../@type/Charge";
import ChargeForm from "../components/ChargeForm.vue";
import {
	type CollectionFacade,
	getMembers,
	useApiRoutes,
} from "../composables/api";
import { useChargeGrouping } from "../composables/useChargeGrouping";

definePageMeta({
	middleware: ["auth"],
});

const { authUser, signOut } = useAuth();
const api = useApiRoutes();
const displayList = ref(true);
const createChargeOpen = ref(false);
const toast = useToast();
const { groupChargesByDay, formatDayLabel } = useChargeGrouping();

// Auth state is automatically managed by the composable

const query = ref();
const { data, error, refresh } = await useAsyncData("userDatas", async () => {
	const [userBanks, charges] = await Promise.all([
		getMembers<Bank>(api.banks.getUserBanks()),
		getMembers<Charge>(api.charges.getCollection(query.value)),
	]);
	return { userBanks, charges };
});

if (data.value?.userBanks && data.value.userBanks.length > 0) {
	query.value = { "bank.id": data.value.userBanks[0]?.id };
}

const sum = computed(() => {
	return data.value?.charges
		?.reduce(
			(partialSum: number, item: Charge) => partialSum + Number(item.amount),
			0,
		)
		.toFixed(2);
});

const sumCurrent = computed(() => {
	return data.value?.charges
		?.reduce(
			(partialSum: number, item: Charge) =>
				partialSum + (!item.state ? Number(item.amount) : 0),
			0,
		)
		.toFixed(2);
});

const selectedBank = ref<number | null>();

const groupedCharges = computed(() => {
	return groupChargesByDay(data.value?.charges || []);
});

const updateQuery = async (id?: number) => {
	if (!id) {
		selectedBank.value = null;
		query.value = {};
	} else {
		selectedBank.value = id;
		query.value = { "bank.id": id };
	}
	await refresh();
};

const submitCharge = async (charge: Partial<Charge>) => {
	try {
		const preparedCharge = {
			...charge,
			amount: charge.amount?.toString(),
			user: `/users/${authUser.value?.id}`,
		};
		await api.charges.create(preparedCharge);
		await refresh();
		createChargeOpen.value = false;
	} catch (e) {
		console.error(e);
	}
};

const updateChargeState = async (charge: { id: string; value: boolean }) => {
	try {
		await api.charges.update(charge.id, { state: charge.value });
		await refresh();
	} catch (e) {
		toast.add({
			color: "error",
			title: "Une erreur est survenue",
			description: String(e),
		});
	}
};

const updateCharge = async (id: string, charge: Partial<Charge>) => {
	try {
		const preparedCharge = {
			...charge,
			amount: charge.amount?.toString(),
		};
		await api.charges.update(id, preparedCharge);
		await refresh();
	} catch (e) {
		console.error(e);
	}
};

const deleteCharge = async (id: string) => {
	try {
		await api.charges.delete(id);
		await refresh();
	} catch (e) {
		console.error(e);
	}
};

const resetCharges = async () => {
	await api.charges.reset();
	await refresh();
};
</script>
