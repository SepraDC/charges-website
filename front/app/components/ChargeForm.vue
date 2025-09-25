<template>
    <UForm
        class="mt-5 flex flex-col gap-3"
        :ref="formComponent"
        :state="chargeState"
        :schema="chargeSchema"
        @submit="submitCharge"
    >
        <UFormField name="name" required>
            <UInput
                v-model="chargeState.name"
                class="w-full"
                placeholder="Nom"
                type="text"
                size="lg"
            />
        </UFormField>
        <FormField name="bank" required>
            <USelect
                v-model="chargeState.bank"
                class="w-full"
                size="lg"
                :avatar="bankItemAvatar"
                :items="bankItems"
                placeholder="Banque"
                value-key="id"
            />
        </FormField>
        <UFormField name="chargeType" required>
            <USelect
                v-model="chargeState.chargeType"
                class="w-full"
                placeholder="Type"
                size="lg"
                :items="chargeTypesItems"
                value-key="id"
            />
        </UFormField>
        <div class="grid grid-cols-2 gap-3">
            <UFormField name="dayOfWithdrawal" required>
                <UInput
                    v-model="chargeState.dayOfWithdrawal"
                    class="w-full"
                    placeholder="Jour de prélèvement"
                    size="lg"
                    type="number"
                    min="1"
                    max="31"
                />
            </UFormField>
            <FormField name="amount">
                <UInput
                    v-model="chargeState.amount"
                    trailing-icon="i-mdi-euro"
                    class="w-full"
                    size="lg"
                    step="0.01"
                    min="0"
                    placeholder="Montant (100€)"
                    type="number"
                />
            </FormField>
        </div>
        <div class="flex w-full items-center justify-center">
            <UButton
                type="submit"
                color="primary"
                size="lg"
                label="Enregistrer"
            />
        </div>
    </UForm>
</template>

<script setup lang="ts">
import type { FormSubmitEvent } from "@nuxt/ui";
import { reactive } from "vue";
import { z } from "zod";
import type { Charge } from "../@type/Charge";
import { getMembers, useApiRoutes } from "../composables/api";

const api = useApiRoutes();

const props = defineProps<{
	initialValue?: Charge;
}>();

const emit = defineEmits<{
	onSubmit: [value: Partial<Charge>];
}>();

const formComponent = ref();

const chargeSchema = z
	.object({
		name: z.string().min(3).max(255),
		amount: z.number().min(0),
		state: z.boolean(),
		bank: z.string(),
		chargeType: z.string(),
		dayOfWithdrawal: z.number().min(1).max(31).optional(),
	})
	.required();

const { data: selectOptions } = await useAsyncData(
	"selectOptions",
	async () => {
		const [banks, chargeTypes] = await Promise.all([
			getMembers(api.banks.getCollection()),
			getMembers(api.chargeTypes.getCollection()),
		]);
		return { banks, chargeTypes };
	},
);

const bankItems = computed(() =>
	selectOptions.value?.banks.map((item) => {
		return {
			label: item.name,
			id: item["@id"],
			avatar: { src: item.image, alt: item.name },
		};
	}),
);

const bankItemAvatar = computed(() => {
	return (
		bankItems.value.find((item) => item.id === chargeState.bank)?.avatar || null
	);
});

const chargeTypesItems = computed(() =>
	selectOptions.value?.chargeTypes.map((item) => {
		return { label: item.name, id: item["@id"] };
	}),
);

const chargeState = reactive({
	name: props.initialValue?.name || "",
	amount: props.initialValue?.amount || 0,
	state: props.initialValue?.state || false,
	bank:
		props.initialValue?.bank?.["@id"] || selectOptions.value?.banks[0]["@id"],
	chargeType:
		props.initialValue?.chargeType?.["@id"] ||
		selectOptions.value?.chargeTypes[0]["@id"],
	dayOfWithdrawal: props.initialValue?.dayOfWithdrawal,
});

const submitCharge = async (event: FormSubmitEvent<Charge>) => {
	const isFormCorrect = chargeSchema.safeParse(chargeState).success;
	if (!isFormCorrect) {
		return;
	}

	emit("onSubmit", event.data);
};
</script>

<style scoped></style>
