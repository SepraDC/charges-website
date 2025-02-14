<template>
	<UForm class="mt-5 flex flex-col gap-3" :ref="formComponent" :state="chargeState" :schema="chargeSchema"
		@submit="submitCharge">
		<UFormField name="name" required>
			<UInput v-model="chargeState.name" class="w-full" placeholder="Nom" type="text" size="lg" />
		</UFormField>
		<FormField name="bank" required>
			<USelect v-model="chargeState.bank" class="w-full" size="lg" :items="bankItems" placeholder="Banque"
				value-key="id" />
		</FormField>
		<UFormField name="chargeType" required>
			<USelect v-model="chargeState.chargeType" class="w-full" placeholder="Type" size="lg"
				:items="chargeTypesItems" value-key="id" />
		</UFormField>
		<div class="grid grid-cols-2 gap-3">
			<UFormField name="date" required>
				<UInput v-model="chargeState.date" class="w-full" placeholder="Date" size="lg" type="date" />
			</UFormField>
			<FormField name="amount">
				<UInput v-model="chargeState.amount" trailing-icon="i-mdi-euro" class="w-full" size="lg" step="0.01"
					min="0" placeholder="Montant (100€)" type="number" />
			</FormField>
		</div>
		<div class="flex w-full items-center justify-center">
			<UButton type="submit" color="primary" size="lg" label="Enregistrer" />
		</div>
	</UForm>
</template>

<script setup lang="ts">
import type { FormSubmitEvent } from "@nuxt/ui";
import FormField from "@nuxt/ui/runtime/components/FormField.vue";
import type { Charge } from "@type/Charge";
import { format } from "date-fns";
import { reactive } from "vue";
import { z } from "zod";
import { getMembers, useApiRoutes } from "../composables/api";

const api = useApiRoutes();

const props = defineProps<{
	initialValue?: Charge;
}>();

const emit = defineEmits<{
	onSubmit: [value];
}>();

const currentUser = ref();

const formComponent = ref();

const { authUser } = useAuthState();
currentUser.value = authUser.value;

const chargeSchema = z
	.object({
		name: z.string().min(3).max(255),
		amount: z.number().min(0),
		state: z.boolean(),
		bank: z.string(),
		chargeType: z.string(),
		date: z.string(),
	})
	.required();

const { data: banks } = useAsyncData("banks", () => {
	return getMembers(api.banks.getCollection());
});

const bankItems = computed(() =>
	banks.value?.map((item) => {
		return { label: item.name, id: item["@id"] };
	}),
);

const { data: chargeTypes } = useAsyncData("chargeTypes", () => {
	return getMembers(api.chargeTypes.getCollection());
});

const chargeTypesItems = computed(() =>
	chargeTypes.value?.map((item) => {
		return { label: item.name, id: item["@id"] };
	}),
);

const chargeState = reactive({
	name: props.initialValue?.name || "",
	amount: props.initialValue?.amount || 0,
	state: props.initialValue?.state || false,
	bank: props.initialValue?.bank?.["@id"] || banks.value?.[0].id,
	chargeType:
		props.initialValue?.chargeType?.["@id"] || chargeTypes.value?.[0].id,
	date: props.initialValue?.date
		? format(new Date(props.initialValue?.date), "yyyy-MM-dd")
		: "",
});

const submitCharge = async (event: FormSubmitEvent<Charge>) => {
	const isFormCorrect = chargeSchema.safeParse(chargeState).success;
	if (!isFormCorrect) {
		return;
	}

	emit("onSubmit", {
		value: {
			...event.data,
			amount: event.data.amount.toString(),
			user: currentUser.value?.["@id"],
		},
	});
};
</script>

<style scoped></style>
